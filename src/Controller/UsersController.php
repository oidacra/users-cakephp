<?php
namespace Acciona\Users\Controller;

use Acciona\Users\Controller\AppController;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Network\Exception\BadRequestException;
use Cake\Utility\Security;
use Cake\Mailer\Email;
use Cake\Log\Log;
use Firebase\JWT\JWT;
/**
 * Users Controller
 *
 * @property \Acciona\Users\Model\Table\UsersTable $Users
 * @property \Acciona\Users\Model\Table\PasswordTokens $PasswordTokens
 * @author Danilo Dominguez Perez
 */
class UsersController extends AppController
{
    /**
     * Initialize the controller and allow token for Jwt authentication
     */
    public function initialize()
    {
        parent::initialize();
        if ($this->Auth) {
            $this->Auth->allow(['login', 'logout', 'passwordRecovery', 'reset', 'user']);
        }

        $config = TableRegistry::exists('PasswordTokens') ? [] : ['className' => 'Acciona\Users\Model\Table\PasswordTokensTable'];
        $this->PasswordTokens = TableRegistry::get('PasswordTokens', $config);
        $this->emailer = new Email();
    }

    protected function getToken($user)
    {
        return [
            'success' => true,
            'data' => [
                'token' => JWT::encode([
                    'sub' => $user['email'],
                    'exp' =>  time() +
                              Configure::read('Users.Token.expiration', 604800)
                ], Security::salt()),
                'id' => $user['id'],
                'administrator' => $user['administrator']
            ],
            '_serialize' => ['success', 'data']
        ];
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->request->allowMethod(['get']);
        $users = $this->paginate($this->Users->find()->contain(['Roles']));

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    public function login()
    {
        $user = $this->Auth->identify();
        if ($this->request->is('post')) {
            if ($user) {
                $this->Auth->setUser($user);
                $this->Flash->success(__('User login'));
                $user = $this->getToken($user);
                if (!$this->isRestCall()) {
                    return $this->redirect($this->Auth->redirectUrl());
                }
            } else {
                $errorMessage = __('Username or password is incorrect');
                $this->Flash->error($errorMessage);
                if ($this->isRestCall()) {
                        throw new UnauthorizedException(__('Invalid user.'));
                }
            }
            /*if (!$this->checkCaptcha()) {
                $this->Flash->error(__('Invalid captcha'));
            }*/
        }
        $this->setData($user);
    }

    public function user()
    {
        $this->request->allowMethod(['get']);
        $user = $this->Auth->identify();
        if ($user) {
            $this->setData($user);
        } else {
            throw new UnauthorizedException(__('User not authenticated.'));
        }
    }

    public function passwordRecovery()
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $token = array_key_exists('token', $this->request->data) ? $this->request->data['token'] : null;
        if ($token == null) {
            throw new BadRequestException(__('Wrong token.'));
        }
        // validate token
        $tokenRecord = $this->Users->PasswordTokens->find()
            ->where(['token' => $token])
            ->first();

        // si no existe, o ya vencio o esta inactivo
        if (!$tokenRecord || $tokenRecord->expiration < time() || $tokenRecord->active == 0) {
            throw new BadRequestException(__('Wrong token or it has already expired.'));
        }

        // validate request has possword
        if (!array_key_exists('password', $this->request->data)) {
            throw new BadRequestException(__('Password was not provided.'));
        }

        $user = $this->Users->get($tokenRecord->user_id, [
            'fields' => ['id']
        ]);
        $user = $this->Users->patchEntity($user, $this->request->data);
        if ($this->Users->saveAndUpdateToken($user, $tokenRecord)) {
            $this->Flash->success(__('The password has been updated.'));
            $user = ['success' => true];
            if (!$this->isRestCall()) {
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->Flash->error(__('The password could not be updated. Please, try again.'));
            $user = [
              'success' => false,
              'errors' => $user->errors(),
            ];
        }

        $this->setData($user);
    }

    public function reset()
    {
        $this->request->allowMethod(['post', 'put']);
        if (isset($this->request->data['email'])) {
            $email = $this->request->data['email'];
            $user = $this->Users->findByEmail($email)->first();
            if ($user) {
                $token = $this->generateAndSaveToken($user->id);

                if ($token && $this->sendRecoveryEmail($token, $user->email)) {
                    $message = __('Message has been sent to your email with
                                    steps to recover your password');
                    $this->Flash->success($message);
                    $data = [
                        'success' => true,
                        'message' => $message
                    ];
                    $this->setData($data);
                    if (!$this->isRestCall()) {
                        return $this->redirect(['action' => 'login']);
                    }
                } else {
                    $message = __('There was an error while sending the
                                    email. Please try again or contact an
                                    administrator.');
                    $this->Flash->error($message);
                    $data = [
                        'success' => false,
                        'message' => $message
                    ];
                }
                $this->setData($data);
            } else {
                $message = __('Incorrect email.');
                $this->Flash->error($message);
                $data = [
                    'success' => false,
                    'message' => $message
                ];
                $this->setData($data);

                if ($this->isRestCall()) {
                    throw new BadRequestException(__('Incorrect email.'));
                }
            }
        } else {
            throw new BadRequestException(__('Please provide an email.'));
        }
    }

    /**
     * Generate and save token for the email
     * @return number | boolean
     */
    protected function generateAndSaveToken($userId)
    {
        $expiration = Configure::read('Users.PasswordRecovery.expiration');

        return $this->PasswordTokens->generateAndSaveToken($userId, $expiration);
    }

    protected function sendRecoveryEmail($token, $email)
    {
        try {
            $template = Configure::read('Users.PasswordRecovery.template');
            $layout = Configure::read('Users.PasswordRecovery.layout');
            $sender = Configure::read('Users.PasswordRecovery.sender');
            $link = Configure::read('Users.PasswordRecovery.link') .
                    '?token=' . $token;

            $Emailer = $this->getEmailer();
            $Emailer->template($template, $layout)
                ->to($email)
                ->subject('Recobrar contraseña')
                ->from($sender)
                ->emailFormat('both')
                ->viewVars(['link' => $link])
                ->send();
        } catch (BadMethodCallException $b) {
            Log::write('error',
                __('An email could not be sent to the address {0}. Error: {1}',
                [$email, $b->getMessage()]));
            return false;
        }

        return true;
    }

    public function checkCaptcha()
    {
        // TODO: implement this
        return true;
    }

    public function logout()
    {
        // TODO: check if this works with Jwt
        $redirectAction = $this->Auth->logout();
        if (!$this->isRestCall()) {
          return $this->redirect($redirectAction);
      } else {
          $this->set(['success' => true]);
      }
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                $user = ['success' => true];
                if (!$this->isRestCall()) {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
                $user = [
                  'success' => false,
                  'errors' => $user->errors()
                ];
            }
        }

        $this->setData($user);
    }

    public function getEmailer() {
        return $this->emailer;
    }

    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->register($user)) {
                $this->Flash->success(
                    __('The user has been registered. An email has been sent to your account or wait for an
                        administrator to make your account active.'));
                $user = ['success' => true];
                if (!$this->isRestCall()) {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The new user could not be registered. Please, try again.'));
                $user = [
                  'success' => false,
                  'errors' => $user->errors()
                ];
            }
        }

        $this->setData($user);
    }

    /**
     * Edit user information
     *
     * @param int|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                $user = ['success' => true];
                if (!$this->isRestCall()) {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
                $user = [
                  'success' => false,
                  'errors' => $user->errors()
                ];
            }
        }

        $this->setData($user);
    }

    /**
     * Update the password of a user
     *
     * @param int|null $id User id
     * @return \Cake\Network\Response|null
     */
    public function editPassword($id = null)
    {
        $user = $this->Users->get($id, [
            'fields' => ['id']
        ]);
        $result = [];
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The password has been updated.'));
                $result = ['success' => true];
                if (!$this->isRestCall()) {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The password could not be updated. Please, try again.'));
                $result = [
                  'success' => false,
                  'errors' => $user->errors(),
                ];
            }
        }

        $this->set($result);
    }

    private function setData($user) {
        if (!$this->isRestCall()) {
          $this->set(compact('user'));
        } else {
          $this->set('user',$user);
        }
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete user from database
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
            $user = ['success' => true];
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
            $user = [
              'success' => false,
              'errors' => $user->errors()
            ];
        }

        if ($this->isRestCall()) {
            $this->setData($user);
        } else {
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Disable user from database
     *
     * @param null $id
     * @param int $active
     * @return \Cake\Network\Response|null
     */
    public function activateUser($id = null, $active = 0) {
        $user = $this->Users->get($id);
        if ($this->request->is(['patch', 'post', 'put']) && $user) {
            $user['User']['active'] = intval($active);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been updated.'));
                $user = ['success' => true];
            } else {
                $this->Flash->error(__('The user could not be updated. Please, try again.'));
                $user = [
                    'success' => false,
                    'errors' => $user->errors()
                ];
            }
        } else if (!$user) {
            throw new BadRequestException(__('Wrong user.'));
        }

        if (!$this->isRestCall()) {
            return $this->redirect(['action' => 'index']);
        } else {
            $this->setData($user);
        }
    }
}
