<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Droplets Model
 *
 * @property \App\Model\Table\BucketsTable&\Cake\ORM\Association\BelongsTo $Buckets
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Droplet newEmptyEntity()
 * @method \App\Model\Entity\Droplet newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Droplet> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Droplet get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Droplet findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Droplet patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Droplet> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Droplet|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Droplet saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Droplet>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Droplet>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Droplet>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Droplet> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Droplet>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Droplet>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Droplet>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Droplet> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DropletsTable extends Table
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

        $this->setTable('droplets');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Buckets', [
            'foreignKey' => 'bucket_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
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
            ->integer('bucket_id')
            ->notEmptyString('bucket_id');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->ascii('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->numeric('amount')
            ->maxLength('amount', 255)
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->boolean('expense')
            ->requirePresence('expense', 'create')
            ->notEmptyString('expense');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['bucket_id'], 'Buckets'), ['errorField' => 'bucket_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
