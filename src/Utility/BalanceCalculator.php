<?php

namespace App\Utility;

use App\Model\Entity\Bucket;
use App\Model\Entity\Droplet;

class BalanceCalculator
{

    //todo 0.x this is vulnerable to off-by-one errors, but since this is a framework learning project,
    // and it doesn't matter for my personal use case I'll leave it as is
    public function calculateNewBucketBalance(Bucket $bucket, Droplet $droplet): string
    {
        bcscale(0);//todo 0.x add support for arbitrary precision

        //todo ? this could probably be added to the Droplet as a dynamic field
        $payerIsPrimary = $bucket->user_primary_id === $droplet->user_id;

        //it would probably be more readable to put this in a series of if/else statements, but this is more fun:P
        $amountModifier = $bucket->primary_user_share_percent;
        $amountModifier = $payerIsPrimary ? $amountModifier : 100 - $amountModifier;
        $amountModifier = $droplet->expense ? $amountModifier : "100";
        $amountModifier = $payerIsPrimary ? $amountModifier : "-" . $amountModifier;
        $amountDiff = bcdiv(bcmul($amountModifier, $droplet->amount), "100");

        return bcadd($bucket->balance, $amountDiff);
    }
}
