<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Bucket;
use Authorization\IdentityInterface;

/**
 * Bucket policy
 */
class BucketPolicy
{
    /**
     * Check if $user can add Bucket
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Bucket $bucket
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Bucket $bucket)
    {
        return true;
    }

    /**
     * Check if $user can edit Bucket
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Bucket $bucket
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Bucket $bucket)
    {
        return $this->isPrimaryUser($user, $bucket);
    }

    /**
     * Check if $user can delete Bucket
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Bucket $bucket
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Bucket $bucket)
    {
        return $this->isPrimaryUser($user, $bucket);
    }

    /**
     * Check if $user can view Bucket
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Bucket $bucket
     * @return bool
     */
    public function canView(IdentityInterface $user, Bucket $bucket)
    {
        return $this->isBucketUser($user, $bucket);
    }

    protected function isPrimaryUser(IdentityInterface $user, Bucket $bucket): bool
    {
        return $user->getIdentifier() === $bucket->user_primary_id;
    }

    protected function isBucketUser(IdentityInterface $user, Bucket $bucket): bool
    {
        return in_array($user->getIdentifier(), [$bucket->user_primary_id, $bucket->user_secondary_id]);
    }
}
