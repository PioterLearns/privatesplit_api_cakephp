<?php

namespace App\Utility\Imports;

use Cake\Http\Exception\BadRequestException;
use Laminas\Diactoros\UploadedFile;

class SplitwiseImportUtility implements ImportUtilityInterface
{

    public function importCsv(
        UploadedFile $file,
        string $external_primary_id,
        int $primaryId,
        int $secondaryId,
        int $primaryUserShare
    ): array
    {
        $resource = $file->getStream()->detach();

        //process headers
        $headers = fgetcsv($resource, escape: '');
        //todo 0.? for now we're trusting the csv format will not change...
        // This is a bit hacky, but I do plan to re-write this, so for now it will work
        if (isset($headers[7])) {
            throw new BadRequestException('Max 3 people groups allowed');
        }
        if (empty($headers[6])) {
            throw new BadRequestException('Minimum 2 people groups allowed');
        }
        $dateIndex = 0;
        $descriptionIndex = 1;
        //todo ? ignoring category for now
        $costIndex = 3;
        $currencyIndex = 4;
        $primaryUserIndex = $headers[5] === $external_primary_id ? 5 : 6;
        if ($headers[$primaryUserIndex] !== $external_primary_id) {
            throw new BadRequestException('Provided external id not present in file!');
        }
        $secondaryUserIndex = $primaryUserIndex === 5 ? 6 : 5;

        //skip empty separator line
        fgetcsv($resource, escape: '');

        $buckets = [];

        bcscale(2);
        //process droplets
        while ($line = fgetcsv($resource, escape: '')) {
            if (empty($line[0])) {
                //separation line -> getting to totals
                break;
            }
            if ($line[$primaryUserIndex] === $line[$secondaryUserIndex]) {
                // this is a non-sharing cost, that we're ignoring
                continue;
            }
            $split = (int)bcmul(bcdiv($line[$primaryUserIndex], $line[$costIndex]), 100);
            $isExpense = abs($split) !== 100;
            if ($split > 0) {
                $payerId = $primaryId;
                $isValidSplit = abs($split - 100) === $primaryUserShare;
            } else {
                $payerId = $secondaryId;
                $isValidSplit = abs($split);
            }

            if (!$isValidSplit && $isExpense) {
                throw new BadRequestException('Found cost split that doesn\'t match defined');
            }

            $currency = $line[$currencyIndex];
            if (empty($buckets[$currency])) {
                $buckets[$currency] = [];
            }

            $buckets[$currency][] = [
                'occurred' => $line[$dateIndex],
                'name' => $line[$descriptionIndex],
                'amount' => $line[$costIndex],
                'user_id' => $payerId,
                'expense' => $isExpense,
            ];
        }

        //validating totals
        while ($line = fgetcsv($resource, escape: '')) {
            //todo 0.4
        }

        return $buckets;
    }
}
