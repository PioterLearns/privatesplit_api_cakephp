<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\DropletsTable&\Cake\ORM\Association\HasMany $Droplets
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\User> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\User> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('PrimaryBuckets', [
            'className' => 'Buckets',
            'foreignKey' => 'user_primary_id',
            'dependent' => true,
        ]);
        $this->hasMany('SecondaryBuckets', [
            'className' => 'Buckets',
            'foreignKey' => 'user_secondary_id',
            'dependent' => true,
        ]);
        $this->hasMany('Droplets', [
            'foreignKey' => 'user_id',
            'dependent' => true,
        ]);
        $this->hasMany('Sessions', [
            'foreignKey' => 'user_id',
            'dependent' => true,
        ]);
    }

    public function findBySessionToken(SelectQuery $query, string $token): SelectQuery
    {
        return $query
            ->select(['Users.id'])
            ->join([
                'Sessions' => [
                    'table' => 'sessions',
                    'type' => 'INNER',
                    'conditions' => 'Sessions.user_id = Users.id',
                ],
            ])
            ->where(['Sessions.token' => $token]);
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
            ->ascii('username')
            ->maxLength('username', 255)
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            //todo 0.4 read-up on https://book.cakephp.org/5.x/orm/validation.html#validation-providers ('provider' => 'table'?)
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->ascii('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->ascii('gpg')
            //todo 0.4 it seems these rules are applied BEFORE _setX modifiers from Entity,
            //     which is a bit weird if you ask me, since this is a DB validation, so it should be done just before
            //     persisting the data in the form it will actually be saved. This being the way it is, allows to mess
            //     with the data in _setX methods that may make it no longer fit validation criteria
            //     Look into https://book.cakephp.org/5.x/orm/table-objects.html#beforerules
//            ->minLength('gpg', 40)
//            ->maxLength('gpg', 64)
            ->requirePresence('gpg', 'create');

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
        $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);

        return $rules;
    }
}
