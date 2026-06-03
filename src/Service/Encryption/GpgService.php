<?php

namespace App\Service\Encryption;

use Cake\Core\Attribute\Configure;
use gnupg;
use RuntimeException;

class GpgService
{
    protected gnupg $gpg;

    public function __construct(
        #[Configure('Gpg.default.keyFingerprint')] protected string $serviceKeyFingerprint,
        #[Configure('Gpg.default.keyPassword')] string $serviceKeyPassword,
        #[Configure('Gpg.default.dir')] string $gpgDir,
        #[Configure('Gpg.errorMode')] int $errorMode,
    ) {
        $this->gpg = new gnupg(["home_dir" => $gpgDir]);
        $this->gpg->seterrormode($errorMode);

        $this->gpg->addsignkey($serviceKeyFingerprint, $serviceKeyPassword);
        $this->gpg->adddecryptkey($serviceKeyFingerprint, $serviceKeyPassword);
    }

    public function encrypt(string $text, array $encryptTo, bool $encryptToSelf = false): string
    {
        if ($encryptToSelf) {
            $this->gpg->addencryptkey($this->serviceKeyFingerprint);
        }
        foreach ($encryptTo as $keyFingerprint) {
            $this->gpg->addencryptkey($keyFingerprint);
        }

        $encrypted = $this->gpg->encryptsign($text);

        $this->gpg->clearencryptkeys();
        if (false === $encrypted) {
            throw new RuntimeException('Data encryption failed!');
        }

        return $encrypted;
    }

    /**
     * We will only be using this for decrypting self-signed data. If we ever implement a functionality
     * that required decrypting user encrypted data, this needs to be extended, to verify given signatures
     *
     * @param string $encrypted
     * @return string
     */
    public function decrypt(string $encrypted): string
    {
        $signature = $this->gpg->decryptverify($encrypted, $decrypted);

        if (false === $signature) {
            throw new RuntimeException("Decrypting failed!");
        }
        if ($signature[0]['fingerprint'] !== $this->serviceKeyFingerprint) {
            throw new RuntimeException("Decrypted data that was signed by someone else!");
        }

        return $decrypted;
    }

    public function import(string $publicKey): string
    {
        $res = $this->gpg->import($publicKey);

        if (false === $res) {
            throw new RuntimeException("Importing key failed!");
        }

        return $res['fingerprint'];
    }

    public function delete(string $fingerprint): bool
    {
        return $this->gpg->deletekey($fingerprint, false);
    }

    public function isEncrypted(string $text): bool
    {
        //todo 1.0 this is vulnerable to users passing "fake" encrypted data, and might lead to issues on frontend,
        // where we try to decrypt an invalid message. Research a better way to validate encryption
        return str_starts_with($text, "-----BEGIN PGP MESSAGE-----");
    }
}
