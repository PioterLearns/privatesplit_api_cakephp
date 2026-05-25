<?php
declare(strict_types=1);

namespace App\Identifier\Resolver;

use ArrayAccess;
use Authentication\Identifier\Resolver\ResolverInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

class SessionResolver implements ResolverInterface
{
    use LocatorAwareTrait;

    public function find(array $conditions, string $type = self::TYPE_AND): ArrayAccess|array|null
    {
        $table = $this->getTableLocator()->get('Users');

        $query = $table->selectQuery();
        return  $query->find('bySessionToken', $conditions['token'])->first();
    }
}
