<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Bucket;
use App\Service\Encryption\GpgService;
use App\Utility\BalanceCalculator;
use App\Utility\Imports\ImportUtilityFactory;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['register']);
        $this->Authorization->skipAuthorization();
    }

    /**
     * Me method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function me()
    {
        $user = $this->request->getAttribute('identity');
        $this->set('user', $this->Users->get($user->id, contain: ['PrimaryBuckets', 'SecondaryBuckets', 'Droplets']));
        $this->viewBuilder()->setOption('serialize', 'user');
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function register(GpgService $gpgService)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $data = $this->request->getData();
        $data['gpg'] = $gpgService->import($data['gpg']);
        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $data);
        if ($this->Users->save($user)) {
            $this->set('user', $user);
            $this->viewBuilder()->setOption('serialize', 'user');
        }
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function login()
    {
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $user = $this->request->getAttribute('identity');
            $session = $this->Users->Sessions->newEmptyEntity();
            $session->setAccess('user_id', true);
            $this->Users->Sessions->patchEntity($session, [
                'token' => Security::randomString(),
                'user_id' => $user->id
            ]);
            if ($this->Users->Sessions->save($session)) {
                $this->set('session', $session);
                $this->viewBuilder()->setOption('serialize', 'session');
            }
        }
    }

    //todo 0.3 this belongs in BucketController...
    public function importData(GpgService $gpgService)
    {
        //todo 0.4 see if this is(post) business can be moved to middleware, or somewhere
        if (false === $this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $data = $this->request->getData();
        $importUtilityFactory = new ImportUtilityFactory();//todo 0.3 DI
        $importUtility = $importUtilityFactory->create($data['type']);

        $userId = $this->request->getAttribute('identity')->id;
        $bucketsData = $importUtility->importCsv(
            $data['attachment'],
            $data['external_self_id'],
            $userId,
            (int)$data['secondary_user_id'],
            (int)$data['primary_user_share'],
        );

        $buckets = [];
        foreach ($bucketsData as $dropletsData) {
            $bucketDatum = [
                'user_primary_id' => $userId,
                'user_secondary_id' => (int)$data['secondary_user_id'],
                'primary_user_share_percent' => (int)$data['primary_user_share'],
                'name' => $data['bucket_name'],
                'balance' => '0'
            ];
            /**
             * @var Bucket $bucket
             */
            $bucket = $this->Users->PrimaryBuckets->newEmptyEntity();
            $bucket->setAccess('balance', true);
            $bucket->setAccess('user_primary_id', true);
            $bucket->setAccess('user_secondary_id', true);
            $bucket->setAccess('primary_user_share_percent', true);

            $this->Users->PrimaryBuckets->patchEntity($bucket, $bucketDatum);
            //todo 0.4 db transaction
            if (!$this->Users->PrimaryBuckets->save($bucket)) {
                //todo 0.4 rollback transaction
            }
            $bucket->decrypt($gpgService);
            $this->hydrateBucket($bucket, $dropletsData);
            if (!$this->Users->PrimaryBuckets->save($bucket)) {
                //todo 0.4 rollback transaction
            }
            $buckets[] = $bucket;
        }

        $this->set('buckets', $buckets);
        $this->viewBuilder()->setOption('serialize', 'buckets');
    }

    private function hydrateBucket(Bucket $bucket, array $dropletsData): void
    {
        $balanceCalculator = new BalanceCalculator();//todo 0.3 DI
        foreach ($dropletsData as $dropletsDatum) {
            $droplet = $this->Users->Droplets->newEmptyEntity();
            $droplet->setAccess('bucket_id', true);
            $droplet->setAccess('user_id', true);
            $dropletsDatum['bucket_id'] = $bucket->id;
            $this->Users->Droplets->patchEntity($droplet, $dropletsDatum);
            //todo 0.4 some mass persist option?
            $newBalance = $balanceCalculator->calculateNewBucketBalance($bucket, $droplet);
            if (!$this->Users->Droplets->save($droplet)) {
                //todo 0.4 rollback transaction
            }
        }
        $this->Users->PrimaryBuckets->patchEntity($bucket, ['balance' => $newBalance]);
    }
}
