<?php

declare(strict_types=1);

namespace App\Utility\Imports;

use Laminas\Diactoros\UploadedFile;

interface ImportUtilityInterface
{
    public function importCsv(
        UploadedFile $file,
        string $external_primary_id,
        int $primaryId,
        int $secondaryId,
        int $primaryUserShare
    ): array;
}
