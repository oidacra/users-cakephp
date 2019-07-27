<?php
namespace Acciona\Users\Controller;

use Acciona\Users\Controller\AppController;

/**
 * Roles Controller
 *
 * @property \Acciona\Users\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $roles = $this->paginate($this->Roles);

        $this->set(compact('roles'));
        $this->set('_serialize', ['roles']);
    }

    /**
     * View method
     *
     * @param string|null $id Role id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => []
        ]);

        $this->set('role', $role);
        $this->set('_serialize', ['role']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->data);
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('The role has been saved.'));
                $role = ['success' => true];
                if (!$this->isRestCall()) {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $role = [
                    'success' => false,
                    'errors' => $role->errors()
                ];
                $this->Flash->error(__('The role could not be saved. Please, try again.'));
            }
        }


        $this->setData($role);

    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->request->data);
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('The role has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The role could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('role'));
        $this->set('_serialize', ['role']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id);
        if ($this->Roles->delete($role)) {
            $this->Flash->success(__('The rol has been deleted.'));
            $role = ['success' => true];
        } else {
            $this->Flash->error(__('The rol could not be deleted. Please, try again.'));
            $role = [
                'success' => false,
                'errors' => $role->errors()
            ];
        }

        if ($this->isRestCall()) {
            $this->setData($role);
        } else {
            return $this->redirect(['action' => 'index']);
        }

    }

    private function setData($roles) {
        if (!$this->isRestCall()) {
            $this->set(compact('roles'));
        } else {
            $this->set('roles',$roles);
        }
        $this->set('_serialize', ['roles']);
    }
    /**
     * lista method
     * Listado de roles
     *
     * @return json
     */
    public function lista(){

        if ($this->request->is('get')) {
            $this->set([
                'success' => true,
                'data' => $this->Roles->find('all')->select(['value'=>'id', 'label'=>'name']),
                '_serialize' => ['success', 'data']
            ]);
        }else{
            throw new BadRequestException('Error listando');
        }

    }

}
