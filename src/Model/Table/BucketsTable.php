<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Buckets Model
 *
 * @property \App\Model\Table\DropletsTable&\Cake\ORM\Association\HasMany $Droplets
 *
 * @method \App\Model\Entity\Bucket newEmptyEntity()
 * @method \App\Model\Entity\Bucket newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Bucket> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bucket get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Bucket findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Bucket patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Bucket> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bucket|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Bucket saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Bucket>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Bucket>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Bucket>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Bucket> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Bucket>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Bucket>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Bucket>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Bucket> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BucketsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('buckets');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Droplets', [
            'foreignKey' => 'bucket_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_primary_id')
            ->requirePresence('user_primary_id', 'create')
            ->notEmptyString('user_primary_id');

        $validator
            ->integer('user_secondary_id')
            ->requirePresence('user_secondary_id', 'create')
            ->notEmptyString('user_secondary_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('balance')
            ->maxLength('balance', 255)
            ->notEmptyString('balance');

        $validator
            ->scalar('primary_user_share_percent')
            ->maxLength('primary_user_share_percent', 255)
            ->notEmptyString('primary_user_share_percent');

        return $validator;
    }
}
