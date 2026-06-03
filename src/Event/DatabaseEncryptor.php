<?php

namespace App\Event;

use App\Service\Encryption\Encryptable;
use App\Service\Encryption\GpgService;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use PDO;

readonly class DatabaseEncryptor implements EventListenerInterface
{
    public function __construct(private GpgService $gpgService)
    {
    }

    public function implementedEvents(): array
    {
        return ['Model.beforeSave' => 'beforeSave'];
    }

    public function beforeSave(EventInterface $event, $entity, $options): void
    {
        if ($entity instanceof Encryptable) {
            $connection = $event->getSubject()->getConnection();
            $entity->encryptBeforeSave(
                $this->gpgService,
                //todo 1.0 there's probably a more optimal way to get fingerprints than SQL, but for now it's fine
                $connection->execute($entity->encryptToIdExtractorSql())->fetchAll(PDO::FETCH_COLUMN)
            );
        }
    }
}
