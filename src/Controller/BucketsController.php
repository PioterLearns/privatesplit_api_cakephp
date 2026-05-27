<?php
declare(strict_types=1);

namespace App\Controller;


use Cake\Http\Exception\BadRequestException;
use RuntimeException;

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
        $query = $this->Buckets->find()->contain(['PrimaryUsers', 'SecondaryUsers']);
        $this->Authorization->applyScope($query);
        $this->set('buckets', $query);
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
        $bucket = $this->Buckets->get($id, contain: ['PrimaryUsers', 'SecondaryUsers', 'Droplets']);
        $this->Authorization->authorize($bucket);
        $this->set('bucket', $bucket);
        $this->viewBuilder()->setOption('serialize', 'bucket');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $this->Authorization->skipAuthorization();
        $bucket = $this->Buckets->newEmptyEntity();
        $bucket->setAccess('user_primary_id', true);
        $bucket->setAccess('user_secondary_id', true);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_primary_id'] = (string)$this->request->getAttribute('identity')->id;
            if ($data['user_primary_id'] === $data['user_secondary_id']) {
                throw new BadRequestException('Cannot share bucket with yourself');
            }
            $bucket = $this->Buckets->patchEntity($bucket, $data);
            if ($this->Buckets->save($bucket)) {
                $this->set('bucket', $bucket);
                $this->viewBuilder()->setOption('serialize', 'bucket');
            }
            //todo 0.3 error handling
        }
        //todo 0.3 add user data to response
//        $primaryUsers = $this->Buckets->PrimaryUsers->find('list', limit: 200)->all();
//        $secondaryUsers = $this->Buckets->SecondaryUsers->find('list', limit: 200)->all();
//        $this->set(compact('bucket', 'primaryUsers', 'secondaryUsers'));
    }
}
