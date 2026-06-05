<?php

declare(strict_types=1);

namespace App\Controller;


use App\Model\Entity\Bucket;
use App\Service\Encryption\GpgService;
use App\Utility\BalanceCalculator;
use App\Utility\Imports\ImportUtilityFactory;
use Cake\Http\Exception\BadRequestException;
use SwaggerBake\Lib\Attribute\OpenApiOperation;
use SwaggerBake\Lib\Attribute\OpenApiResponse;

/**
 * Buckets Controller
 *
 * @property \App\Model\Table\BucketsTable $Buckets
 */
class BucketsController extends AppController
{
    #[OpenApiResponse(
        statusCode: '40x',
        ref: '#/components/schemas/Error'
    )]
    /**
     * List buckets that user is participating in
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


    #[OpenApiResponse(
        statusCode: '40x',
        ref: '#/components/schemas/Error'
    )]
    /**
     * View bucket details
     *
     * @param string|null $id Bucket id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $bucket = $this->Buckets->get($id, contain: ['PrimaryUsers', 'SecondaryUsers', 'Droplets']);
        $this->Authorization->authorize($bucket);
        $this->set('bucket', $bucket);
        $this->viewBuilder()->setOption('serialize', 'bucket');
    }

    #[OpenApiResponse(
        statusCode: '201',
    )]
    #[OpenApiResponse(
        statusCode: '40x',
        ref: '#/components/schemas/Error'
    )]
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
        //todo 0.4 error handling
        //todo 0.4 201 handling
    }

    #[OpenApiOperation(
        summary: 'Import external data',
        description: 'Parses data from a provided export files, from other services (for not only Splitwise)',
        tagNames: ['Buckets', 'Droplets'],
    )]
    #[OpenApiResponse(schemaType: 'array', ref: '#/components/schemas/Bucket')]
    #[OpenApiResponse(
        statusCode: '40x',
        ref: '#/components/schemas/Error'
    )]
    /**
     * @param GpgService $gpgService
     * @return void
     */
    public function importData(GpgService $gpgService)
    {
        $this->Authorization->skipAuthorization();
        $data = $this->request->getData();
        $importUtilityFactory = new ImportUtilityFactory();
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
            $bucket = $this->Buckets->newEmptyEntity();
            $bucket->setAccess('balance', true);
            $bucket->setAccess('user_primary_id', true);
            $bucket->setAccess('user_secondary_id', true);
            $bucket->setAccess('primary_user_share_percent', true);

            $this->Buckets->patchEntity($bucket, $bucketDatum);
            //todo 0.4 db transaction
            if (!$this->Buckets->save($bucket)) {
                //todo 0.4 rollback transaction
            }
            $bucket->decrypt($gpgService);
            $this->hydrateBucket($bucket, $dropletsData);
            if (!$this->Buckets->save($bucket)) {
                //todo 0.4 rollback transaction
            }
            $buckets[] = $bucket;
        }

        $this->set('buckets', $buckets);
        $this->viewBuilder()->setOption('serialize', 'buckets');
        //todo 0.4 20x response. Not sure about using 201 for a potential synchronous import that creates multiple
        // resources, as 201 requires Location header, and I don't feel like implementing a method that fetches
        // multiple buckets just for that. I'll think about it later
    }

    private function hydrateBucket(Bucket $bucket, array $dropletsData): void
    {
        $balanceCalculator = new BalanceCalculator();
        foreach ($dropletsData as $dropletsDatum) {
            $droplet = $this->Buckets->Droplets->newEmptyEntity();
            $droplet->setAccess('bucket_id', true);
            $droplet->setAccess('user_id', true);
            $dropletsDatum['bucket_id'] = $bucket->id;
            $this->Buckets->Droplets->patchEntity($droplet, $dropletsDatum);
            //todo 0.4 some mass persist option?
            $newBalance = $balanceCalculator->calculateNewBucketBalance($bucket, $droplet);
            if (!$this->Buckets->Droplets->save($droplet)) {
                //todo 0.4 rollback transaction
            }
        }
        $this->Buckets->patchEntity($bucket, ['balance' => $newBalance]);
    }
}
