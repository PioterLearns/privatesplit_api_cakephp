<?php

declare(strict_types=1);

namespace App\Policy;

/**
 * Bucket policy
 */
class BucketsTablePolicy
{
    /**
     * Apply user access controls to a query for index actions
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Cake\ORM\Query\SelectQuery $query The query to apply authorization conditions to.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function scopeIndex($user, $query)
    {
        //todo 0.3 https://book.cakephp.org/authorization/3/component.html#applying-policy-scopes ?
        return $query->where(
            [
                'OR' => [
                    ['Buckets.user_primary_id' => $user->getIdentifier()],
                    ['Buckets.user_secondary_id' => $user->getIdentifier()]
                ]
            ]
        );
    }
}
