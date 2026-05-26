<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Droplet;

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
            if ($this->Droplets->save($droplet)) {
                //todo 0.3 db transactions?
                $this->bucketBalanceAdjustment($droplet);
                //todo ? add new balance to response?
                $this->set('droplet', $droplet);
                $this->viewBuilder()->setOption('serialize', 'droplet');
            }
            //todo 0.3 error handling https://book.cakephp.org/5.x/development/errors.html
        }
    }

    //todo ? this is vulnerable to off-by-one errors, but since this is a framework learning project,
    // and it doesn't matter for my personal use case I'll leave it as is

    //todo 0.3 extract this logic to a unit-testable method (return difference to apply to db instead of doing it)
    private function bucketBalanceAdjustment(Droplet $droplet): void
    {
        bcscale(0);//todo ? add support for arbitrary precision

        $bucket = $this->Droplets->Buckets->get($droplet->bucket_id);

        //todo ? this could probably be added to the Droplet as a dynamic field
        $payerIsPrimary = $bucket->user_primary_id === $droplet->user_id;

        //it would probably be more readable to put this in a series of if/else statements, but this is more fun:P
        $amountModifier = $bucket->primary_user_share_percent;
        $amountModifier = $payerIsPrimary ? $amountModifier : 100 - $amountModifier;
        $amountModifier = $droplet->expense ? $amountModifier : "100";
        $amountModifier = $payerIsPrimary ? $amountModifier : "-" . $amountModifier;
        $amountDiff = bcdiv(bcmul($amountModifier, $droplet->amount), "100");
        $newBalance = bcadd($bucket->balance, $amountDiff);

        $bucket->setAccess('balance', true);
        $this->Droplets->Buckets->patchEntity($bucket, ['balance' => $newBalance]);
        if (false === $this->Droplets->Buckets->save($bucket)) {
            //todo 0.3 rollback non-existing transaction ;)
        }
    }
}
