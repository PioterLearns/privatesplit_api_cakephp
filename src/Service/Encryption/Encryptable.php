<?php

namespace App\Service\Encryption;

interface Encryptable
{

    /**
     * Updated fields that require encryption for entities. Triggered on beforeSave, through dedicated listener
     *
     * @param GpgService $gpgService
     * @param array $encryptTo array of gpg fingerprints, to use as encryptTo arguments
     * @return void
     */
    public function encryptBeforeSave(GpgService $gpgService, array $encryptTo): void;

    /**
     * Decrypts field that our system has access to. Done on demand, since most of the time, we just want to return
     * data to user, which they should be able to decrypt on whatever frontend they're using
     *
     * @param GpgService $gpgService
     * @return void
     */
    public function decrypt(GpgService $gpgService): void;

    /**
     * Returns SQL that should SELECT all relevant gpg fingerprints from users table.
     *
     * !!! WARNING !!! The query is ran as is, without SQLi protection! Make sure to only use safe data in WHERE
     * todo 1.0 make this more resistant to potential future changes, that could lead to SQLi
     *
     * @return string - Full SQL string with inserted arguments
     */
    public function encryptToIdExtractorSql(): string;
}
