<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Buckets Controller
 *
 * @property \App\Model\Table\BucketsTable $Buckets
 */
class BucketsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Buckets->find()
            ->contain(['PrimaryUsers', 'SecondaryUsers']);
        $buckets = $this->paginate($query);

        $this->set(compact('buckets'));
    }

    /**
     * View method
     *
     * @param string|null $id Bucket id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bucket = $this->Buckets->get($id, contain: ['PrimaryUsers', 'SecondaryUsers', 'Droplets']);
        $this->set(compact('bucket'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bucket = $this->Buckets->newEmptyEntity();
        if ($this->request->is('post')) {
            $bucket = $this->Buckets->patchEntity($bucket, $this->request->getData());
            if ($this->Buckets->save($bucket)) {
                $this->Flash->success(__('The bucket has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The bucket could not be saved. Please, try again.'));
        }
        $primaryUsers = $this->Buckets->PrimaryUsers->find('list', limit: 200)->all();
        $secondaryUsers = $this->Buckets->SecondaryUsers->find('list', limit: 200)->all();
        $this->set(compact('bucket', 'primaryUsers', 'secondaryUsers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Bucket id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $bucket = $this->Buckets->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $bucket = $this->Buckets->patchEntity($bucket, $this->request->getData());
            if ($this->Buckets->save($bucket)) {
                $this->Flash->success(__('The bucket has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The bucket could not be saved. Please, try again.'));
        }
        $primaryUsers = $this->Buckets->PrimaryUsers->find('list', limit: 200)->all();
        $secondaryUsers = $this->Buckets->SecondaryUsers->find('list', limit: 200)->all();
        $this->set(compact('bucket', 'primaryUsers', 'secondaryUsers'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Bucket id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bucket = $this->Buckets->get($id);
        if ($this->Buckets->delete($bucket)) {
            $this->Flash->success(__('The bucket has been deleted.'));
        } else {
            $this->Flash->error(__('The bucket could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
