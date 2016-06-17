<?php
namespace Acciona\Users\Controller;

use Acciona\Users\Controller\AppController;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Network\Exception\BadRequestException;
use Cake\Utility\Security;
use Cake\Mailer\Email;
use Cake\Log\Log;
use Firebase\JWT\JWT;
/**
 * Users Controller
 *
 * @property \Users\Model\Table\UsersTable $Users
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
            $this->Auth->allow(['login', 'logout', 'passwordRecovery', 'reset']);
        }
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
                ], Security::salt())
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
        $users = $this->paginate($this->Users);

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
        // TODO: should validate token received and password
        // use token to get id of user
    }

    public function reset($email = null)
    {
        $this->request->allowMethod(['post', 'put']);
        if ($email) {
            $user = $this->Users->findByEmail($email);
            if ($user) {
                $token = $this->generateToken($user['email']);
                if ($token && $this->sendRecoveryEmail($token, $user['email'])) {
                    $message = __('Message has been sent to your email with
                                    steps to recover your password');
                    $this->Flash->success($message);
                    $data = [
                        'success' => true,
                        'message' => $message
                    ];

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
            }
        } else {
            throw new BadRequestException(__('An email should be sent.'));
        }
    }

    /**
     * Generate and save token for the email
     * @return number | null
     */
    protected function generateAndSaveToken($email)
    {
        // TODO: generate token and save it in a database
        $expiration = Configure::read('Users.PasswordRecovery.expiration');
        $token = '';

        return $token;
    }

    protected function sendRecoveryEmail($email, $token)
    {
        try {
            $template = Configure::read('Users.PasswordRecovery.template');
            $layout = Configure::read('Users.PasswordRecovery.layout');
            $sender = Configure::read('Users.PasswordRecovery.sender');
            $link = Configure::read('Users.PasswordRecovery.link') .
                    '?token=' . $token;

            $emailer = new Email();
            $email->template($template, $layout)
                ->to($email)
                ->from($sender)
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
          return $this->redirect($redictectAction);
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
            'contain' => []
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
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
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
        }

        $this->setData($user);
    }

    private function setData($user) {
        if (!$this->isRestCall()) {
          $this->set(compact('user'));
          $this->set('_serialize', ['user']);
        } else {
          $this->set($user);
          //$this->set('_serialize', ['response']);
        }
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
