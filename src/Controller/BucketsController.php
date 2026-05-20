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
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        //todo auth->filter
        $this->set('buckets', $this->Buckets->find()->contain(['PrimaryUsers', 'SecondaryUsers']));
        $this->viewBuilder()->setOption('serialize', 'buckets');
    }

    /**
     * View method
     *
     * @param string|null $id Bucket id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        //todo auth
        $this->set('bucket', $this->Buckets->get($id, contain: ['PrimaryUsers', 'SecondaryUsers', 'Droplets']));
        $this->viewBuilder()->setOption('serialize', 'bucket');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        //todo auth
        //todo validate
        $bucket = $this->Buckets->newEmptyEntity();
        //todo research $_accessible a bit more. I think I saw some sort of "don't apply when new" somewhere in docs
        $bucket->setAccess('user_primary_id', true);
        $bucket->setAccess('user_secondary_id', true);
        if ($this->request->is('post')) {
            $bucket = $this->Buckets->patchEntity($bucket, $this->request->getData());
            if ($this->Buckets->save($bucket)) {
                $this->set('bucket', $bucket);
                $this->viewBuilder()->setOption('serialize', 'bucket');
            }
            //todo error handling
        }
        //todo add user data to response
//        $primaryUsers = $this->Buckets->PrimaryUsers->find('list', limit: 200)->all();
//        $secondaryUsers = $this->Buckets->SecondaryUsers->find('list', limit: 200)->all();
//        $this->set(compact('bucket', 'primaryUsers', 'secondaryUsers'));
    }
}
