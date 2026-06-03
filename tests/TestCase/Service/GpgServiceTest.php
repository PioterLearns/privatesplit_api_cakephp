<?php

namespace App\Test\TestCase\Service;

use App\Service\Encryption\GpgService;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use gnupg;

class GpgServiceTest extends TestCase
{

    protected GpgService $gpgService;
    protected gnupg $gpgRaw;

    protected string $serviceKeyFingerprint;
    protected string $servicePassword;
    protected string $testKeyFingerprint = 'EE3C90B4B7EAD77599D72B19BA22A325733583AB';
    protected string $testKey = <<<PUBKEY
                     -----BEGIN PGP PUBLIC KEY BLOCK-----

                     mDMEahz5PxYJKwYBBAHaRw8BAQdAt6qeBK1vnb2DeUWnS+siUKtpBXce9qUja3C9
                     oewOr0q0B1Rlc3RBUEmIkAQTFgoAOBYhBO48kLS36td1mdcrGboioyVzNYOrBQJq
                     HPk/AhsDBQsJCAcCBhUKCQgLAgQWAgMBAh4BAheAAAoJELoioyVzNYOryWoBAKtX
                     Kqyqxc8mMYskB4tuK9eWpEX9hbnnEeFmhntnyQvNAP9qWNXgaanh4sHNmZOm1WY3
                     gGAqOWQCNWKUiUWeCGsmArg4BGoc+T8SCisGAQQBl1UBBQEBB0CQfsCi4ouPwjvJ
                     QiVdO/LsQcNsMXNIzI35vq3WAa4SMQMBCAeIeAQYFgoAIBYhBO48kLS36td1mdcr
                     GboioyVzNYOrBQJqHPk/AhsMAAoJELoioyVzNYOrZokA/1GeIVOqtixzaS08xrbT
                     e1PVKj2N1xkbB2+WpbDW4oAQAQDM4M4pDqzA6ne1caybb4Pl9KCU6ViSpv7Q+QkW
                     epCRBg==
                     =2xWK
                     -----END PGP PUBLIC KEY BLOCK-----
                     PUBKEY;

    public function setUp(): void
    {
        parent::setUp();

        $homeDir = Configure::read('Gpg.test.dir');
        $this->serviceKeyFingerprint = Configure::read('Gpg.test.keyFingerprint');
        $this->servicePassword = Configure::read('Gpg.test.keyPassword');

        //both objects will be using the same gpg agent under the hood, so it's not a fully reliable test,
        // but it's still better than relying on tested Service logic for assertions
        $this->gpgService = new GpgService(
            $this->serviceKeyFingerprint,
            $this->servicePassword,
            $homeDir,
            Configure::read('Gpg.errorMode'),
        );
        $this->gpgRaw = new gnupg(["home_dir" => $homeDir]);

        //todo 0.4 find a graceful way to handle service test key. Right now it requires manual setup.
        // Probably a Command, since writing a Command is on my "to learn" list anyway
        // Would actually be handy for default key setup too
        // Especially since gpg agent seems wonky, and uses keys he's not asked to for operations,
        // so additional gpg.conf with default-key needs to be set up as a fallback
    }

    public function testImport_validKeyProvided_importSuccessful(): void
    {
        //make sure it's not already imported from previous test runs
        $this->gpgRaw->deletekey($this->testKeyFingerprint, false);
        $keyNotImportedCheck = $this->gpgRaw->keyinfo($this->testKeyFingerprint);
        if (!empty($keyNotImportedCheck)) {
            throw new \RuntimeException("Cannot test import! Key already imported...");
        }

        $res = $this->gpgService->import($this->testKey);

        $keyInfo = $this->gpgRaw->keyinfo($this->testKeyFingerprint);
        $this->assertNotEmpty($keyInfo);
    }

    public function testImport_validKeyProvided_fingerprintReturned(): void
    {
        //make sure it's not already imported from previous test runs
        $this->gpgRaw->deletekey($this->testKeyFingerprint, false);
        $keyNotImportedCheck = $this->gpgRaw->keyinfo($this->testKeyFingerprint);
        if (!empty($keyNotImportedCheck)) {
            throw new \RuntimeException("Cannot test import! Key already imported...");
        }

        $res = $this->gpgService->import($this->testKey);

        $this->assertEquals($this->testKeyFingerprint, $res);
    }

    public function testImport_invalidKeyProvided_exceptionThrown(): void
    {
        $this->expectExceptionMessage('Importing key failed!');
        $this->gpgService->import("invalid key");
    }

    public function testDelete_keyPresent_removalSuccessful(): void
    {
        $keyImportedCheck = $this->gpgRaw->import($this->testKey);
        if (false === $keyImportedCheck) {
            throw new \RuntimeException("Cannot test delete, due to key not importing correctly...");
        }

        $res = $this->gpgService->delete($this->testKeyFingerprint);

        $this->assertTrue($res);
        $keyAbsentCheck = $this->gpgRaw->keyinfo($this->testKeyFingerprint);
        $this->assertEmpty($keyAbsentCheck);
    }

    public function testDelete_keyNotPresent_removalFailed(): void
    {
        $this->gpgRaw->deletekey($this->testKeyFingerprint, true);
        $keyAbsentCheck = $this->gpgRaw->keyinfo($this->testKeyFingerprint);
        if (!empty($keyAbsentCheck)) {
            throw new \RuntimeException("Cannot test delete of non existing key. Key not deletable...");
        }

        $res = @$this->gpgService->delete($this->testKeyFingerprint);

        $this->assertFalse($res);
    }

    public function testDecrypt_decryptKeyAvailable_messageDecrypted(): void
    {
        $testMessage = "test message";
        $this->gpgRaw->addencryptkey($this->serviceKeyFingerprint);
        $encryptedMessage = $this->gpgRaw->encryptsign($testMessage);
        if (false === $encryptedMessage) {
            throw new \RuntimeException("Cannot test decrypt. Encrypting failed...");
        }

        $res = $this->gpgService->decrypt($encryptedMessage);

        $this->assertEquals($testMessage, $res);
    }

    public function testDecrypt_decryptKeyUnavailable_decryptionFailed(): void
    {
        $testMessage = "test message";
        $this->gpgRaw->import($this->testKey);
        $this->gpgRaw->addencryptkey($this->testKeyFingerprint);
        $encryptedMessage = $this->gpgRaw->encryptsign($testMessage);
        if (false === $encryptedMessage) {
            throw new \RuntimeException("Cannot test decrypt. Encrypting failed...");
        }

        $this->expectExceptionMessage('Decrypting failed!');
        @$this->gpgService->decrypt($encryptedMessage);
    }

    public function testDecrypt_multipleDecryptsW_decryptionFailed(): void
    {
        $testMessage = "test message";
        $this->gpgRaw->import($this->testKey);
        $this->gpgRaw->addencryptkey($this->testKeyFingerprint);
        $encryptedMessage = $this->gpgRaw->encryptsign($testMessage);
        if (false === $encryptedMessage) {
            throw new \RuntimeException("Cannot test decrypt. Encrypting failed...");
        }

        $this->expectExceptionMessage('Decrypting failed!');
        @$this->gpgService->decrypt($encryptedMessage);
    }

    public function testEncrypt_validFingerprintsProvided_messageIsPossibleToDecryptWithCorrectKey(): void
    {
        $message = "test message";

        $encrypted = $this->gpgService->encrypt($message, [$this->testKeyFingerprint, $this->serviceKeyFingerprint]);

        $this->assertEquals($message, $this->gpgRaw->decrypt($encrypted));
    }

    public function testEncrypt_validFingerprintsProvided_messageIsImpossibleToDecryptWithoutValidKey(): void
    {
        $message = "test message";

        $encrypted = $this->gpgService->encrypt($message, [$this->testKeyFingerprint]);

        $this->assertFalse($this->gpgRaw->decrypt($encrypted));
    }

    public function testEncrypt_encryptToSelfToggled_messageIsPossibleToDecryptWithServiceKey(): void
    {
        $message = "test message";

        $encrypted = $this->gpgService->encrypt($message, [$this->testKeyFingerprint], true);

        $this->assertEquals($message, $this->gpgRaw->decrypt($encrypted));
    }

    public function testEncrypt_multipleEncryptsToDifferentAddressees_addressesAreClearedBetweenEncrypts(): void
    {
        $message = "test message";

        $encrypted = $this->gpgService->encrypt($message, [$this->testKeyFingerprint, $this->serviceKeyFingerprint]);

        $this->assertEquals($message, $this->gpgRaw->decrypt($encrypted));

        $encrypted = $this->gpgService->encrypt($message, [$this->testKeyFingerprint]);

        $this->assertFalse($this->gpgRaw->decrypt($encrypted));
    }
}
