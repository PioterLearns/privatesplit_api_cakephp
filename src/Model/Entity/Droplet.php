<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Service\Encryption\Encryptable;
use App\Service\Encryption\GpgService;
use Cake\ORM\Entity;

/**
 * Droplet Entity
 *
 * @property int $id
 * @property int $bucket_id
 * @property int $user_id
 * @property string $name
 * @property string $amount
 * @property bool $expense
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Bucket $bucket
 * @property \App\Model\Entity\User $user
 */
class Droplet extends Entity implements Encryptable
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'amount' => true,//todo 0.4 changing this requires an update in bucket. If we can't force this somehow - remove this
        'expense' => true,//todo 0.4 changing this requires an update in bucket. If we can't force this somehow - remove this
        'occurred' => true,
    ];

    public function encryptBeforeSave(GpgService $gpgService, array $encryptTo): void
    {
        //always encrypt "amount"; allow system to decrypt
        if ($this->isDirty('amount')) {
            $this->set(
                'amount',
                $gpgService->encrypt(
                    $this->amount,
                    $encryptTo,
                    true
                )
            );
        }

        //encrypt "name" when it comes from imports (unencrypted); Do NOT allow system to decrypt
        if (
            $this->isNew()
            && false === $gpgService->isEncrypted($this->name)
        ) {
            $this->set(
                'name',
                $gpgService->encrypt(
                    $this->name,
                    $encryptTo
                )
            );
        }
    }

    public function encryptToIdExtractorSql(): string
    {
        return "
            SELECT u.gpg
            FROM users u
                 JOIN buckets b ON b.user_primary_id = u.id
                                OR b.user_secondary_id = u.id
            WHERE b.id = {$this->bucket_id}";
    }

    public function decrypt(GpgService $gpgService): void
    {
        if ($gpgService->isEncrypted($this->amount)) {
            $this->set(
                'amount',
                $gpgService->decrypt($this->amount)
            );
        }
    }
}
