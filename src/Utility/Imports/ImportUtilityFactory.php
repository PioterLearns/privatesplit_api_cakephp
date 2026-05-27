<?php

namespace App\Utility\Imports;

use Cake\Http\Exception\BadRequestException;

class ImportUtilityFactory
{
    private static array $TYPE_MAPPING = [
        'splitwise' => SplitwiseImportUtility::class
    ];

    public function create(string $type): ImportUtilityInterface
    {
        if (empty(self::$TYPE_MAPPING[$type])) {
            throw new BadRequestException('Unknown import type');
        }

        return new self::$TYPE_MAPPING[$type];
    }
}
