<?php
declare(strict_types=1);

namespace App\Model\Entity;

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
class Droplet extends Entity
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
        'amount' => true,//todo 0.3 changing this requires an update in bucket. If we can't force this somehow - remove this
        'expense' => true,//todo 0.3 changing this requires an update in bucket. If we can't force this somehow - remove this
        'occurred' => true,
    ];
}
