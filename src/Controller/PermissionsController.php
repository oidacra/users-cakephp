<?php
namespace Acciona\Users\Controller;

use Acciona\Users\Controller\AppController;

/**
 * Permissions Controller
 * @property \Acciona\Users\Model\Table\PermissionsTable $Permissions
 */
class PermissionsController extends AppController
{
    /**
     * Initialize the controller and allow permissions
     */
    public function initialize()
    {
        parent::initialize();
        if ($this->Auth) {
            $this->Auth->allow(['permissions']);
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $permissions = $this->paginate($this->Permissions);

        $this->set(compact('permissions'));
        $this->set('_serialize', ['permissions']);
    }

    /**
     * Return the permissions of the current user
     *
     * @return \Cake\Network\Response|null
     */
    public function permissions()
    {
        $this->request->allowMethod(['get']);
        $user = $this->Auth->identify();
        if ($user) {
            $actions = $this->Permissions->getUserActions($user['id']);
            $this->set(compact('actions'));
            $this->set('_serialize', ['actions']);
        } else {
            throw new UnauthorizedException(__('User not authenticated.'));
        }
    }

    /**
     * View method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $permission = $this->Permissions->get($id, [
            'contain' => []
        ]);

        $this->set('permission', $permission);
        $this->set('_serialize', ['permission']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $permission = $this->Permissions->newEntity();
        if ($this->request->is('post')) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->data);
            if ($this->Permissions->save($permission)) {
                $this->Flash->success(__('The permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The permission could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $permission = $this->Permissions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->data);
            if ($this->Permissions->save($permission)) {
                $this->Flash->success(__('The permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The permission could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permission = $this->Permissions->get($id);
        if ($this->Permissions->delete($permission)) {
            $this->Flash->success(__('The permission has been deleted.'));
        } else {
            $this->Flash->error(__('The permission could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
