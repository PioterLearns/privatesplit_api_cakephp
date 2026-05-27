<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Bucket;
use App\Model\Entity\Droplet;
use Authorization\IdentityInterface;

/**
 * Droplet policy
 */
class DropletPolicy
{
    /**
     * Check if $user can add Droplet
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Droplet $droplet
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Bucket $bucket)
    {
        return $this->isBucketUser($user, $bucket);
    }

    /**
     * Check if $user can edit Droplet
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Droplet $droplet
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Droplet $droplet)
    {
        return $this->isDropletCreator($user, $droplet);
    }

    /**
     * Check if $user can delete Droplet
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Droplet $droplet
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Droplet $droplet)
    {
        return $this->isDropletCreator($user, $droplet);
    }

    /**
     * Check if $user can view Droplet
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Droplet $droplet
     * @return bool
     */
    public function canView(IdentityInterface $user, Bucket $bucket)
    {
        return $this->isBucketUser($user, $bucket);
    }

    protected function isBucketUser(IdentityInterface $user, Bucket $bucket): bool
    {
        return in_array($user->getIdentifier(), [
            $bucket->user_primary_id,
            $bucket->user_secondary_id
        ], true);
    }

    protected function isDropletCreator(IdentityInterface $user, Droplet $droplet): bool
    {
        return $user->getIdentifier() === $droplet->user_id;
    }
}
