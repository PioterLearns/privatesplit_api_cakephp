<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Service\Encryption\Encryptable;
use App\Service\Encryption\GpgService;
use Cake\ORM\Entity;

/**
 * Bucket Entity
 *
 * @property int $id
 * @property int $user_primary_id
 * @property int $user_secondary_id
 * @property string $name
 * @property string $balance
 * @property string $primary_user_share_percent
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Droplet[] $droplets
 */
class Bucket extends Entity implements Encryptable
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
    ];

    public function encryptBeforeSave(GpgService $gpgService, array $encryptTo): void
    {
        //always encrypt "balance"; allow system to decrypt
        if (
            $this->isDirty('balance')
            || $this->isNew()
        ) {
            $this->set(
                'balance',
                $gpgService->encrypt(
                    $this->balance ?? '0',
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
            WHERE u.id = {$this->user_primary_id}
               OR u.id = {$this->user_secondary_id}";
    }

    public function decrypt(GpgService $gpgService): void
    {
        if ($gpgService->isEncrypted($this->balance)) {
            $this->set(
                'balance',
                $gpgService->decrypt($this->balance)
            );
        }
    }
}
