<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Droplets Controller
 *
 * @property \App\Model\Table\DropletsTable $Droplets
 */
class DropletsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Droplets->find()
            ->contain(['Buckets', 'Users']);
        $droplets = $this->paginate($query);

        $this->set(compact('droplets'));
    }

    /**
     * View method
     *
     * @param string|null $id Droplet id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $droplet = $this->Droplets->get($id, contain: ['Buckets', 'Users']);
        $this->set(compact('droplet'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $droplet = $this->Droplets->newEmptyEntity();
        if ($this->request->is('post')) {
            $droplet = $this->Droplets->patchEntity($droplet, $this->request->getData());
            if ($this->Droplets->save($droplet)) {
                $this->Flash->success(__('The droplet has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The droplet could not be saved. Please, try again.'));
        }
        $buckets = $this->Droplets->Buckets->find('list', limit: 200)->all();
        $users = $this->Droplets->Users->find('list', limit: 200)->all();
        $this->set(compact('droplet', 'buckets', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Droplet id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $droplet = $this->Droplets->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $droplet = $this->Droplets->patchEntity($droplet, $this->request->getData());
            if ($this->Droplets->save($droplet)) {
                $this->Flash->success(__('The droplet has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The droplet could not be saved. Please, try again.'));
        }
        $buckets = $this->Droplets->Buckets->find('list', limit: 200)->all();
        $users = $this->Droplets->Users->find('list', limit: 200)->all();
        $this->set(compact('droplet', 'buckets', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Droplet id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $droplet = $this->Droplets->get($id);
        if ($this->Droplets->delete($droplet)) {
            $this->Flash->success(__('The droplet has been deleted.'));
        } else {
            $this->Flash->error(__('The droplet could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
