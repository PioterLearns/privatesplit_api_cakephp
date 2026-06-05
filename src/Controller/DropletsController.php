<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utility\BalanceCalculator;

/**
 * Droplets Controller
 *
 * @property \App\Model\Table\DropletsTable $Droplets
 */
class DropletsController extends AppController
{

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
        $this->Authorization->authorize($droplet->bucket);
        $this->set('droplet', $droplet);
        $this->viewBuilder()->setOption('serialize', 'droplet');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $bucket = $this->Droplets->Buckets->get($data['bucket_id']);
            $this->Authorization->authorize($bucket);
            $droplet = $this->Droplets->newEmptyEntity();
            $droplet->setAccess('user_id', true);
            $droplet->setAccess('bucket_id', true);
            $droplet->setAccess('expense', true);
            $data['user_id'] = (string)$this->request->getAttribute('identity')->id;
            $droplet = $this->Droplets->patchEntity($droplet, $data);
            $balanceCalculator = new BalanceCalculator();
            $newBalance = $balanceCalculator->calculateNewBucketBalance($bucket, $droplet);
            if ($this->Droplets->save($droplet)) {
                //todo 0.4 db transactions
                $bucket->setAccess('balance', true);
                $this->Droplets->Buckets->patchEntity($bucket, ['balance' => $newBalance]);
                if (false === $this->Droplets->Buckets->save($bucket)) {
                    //todo 0.4 rollback non-existing transaction ;)
                }
                $this->set('droplet', $droplet);
                $this->viewBuilder()->setOption('serialize', 'droplet');
            }
        }
    }

}
