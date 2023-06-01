<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model;

use Autoprotect\DynamodbODM\Model\Encryptor\EncryptorInterface;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

trait EncryptorTrait
{
    protected function getEncryptor(): EncryptorInterface
    {
        return (new class implements EncryptorInterface {
            protected const ENCRYPTION_KEY = 'def000008053addc0f94b14c0e480a10631a0a970b3565e5a7a2aeaeeb51a39e2d139a8977bc02be0195f0036a29aefff9df6d2ddb81432d14b4dce82b83b3a95c6d0205';

            public function decrypt(string|array $encryptedData, array $options = []): string|array
            {
                if (is_array($encryptedData)) {
                    return $encryptedData;
                }
                return Crypto::decrypt(
                    $encryptedData,
                    Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY)
                );
            }

            public function encrypt(string|array $data, array $options = []): string|array
            {
                if (is_array($data)) {
                    return $data;
                }
                return Crypto::encrypt(
                    $data,
                    Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY),
                );
            }
        });
    }

    protected function getScalarArrayEncryptor(): EncryptorInterface
    {
        return (new class implements EncryptorInterface {
            protected const ENCRYPTION_KEY = 'def000008053addc0f94b14c0e480a10631a0a970b3565e5a7a2aeaeeb51a39e2d139a8977bc02be0195f0036a29aefff9df6d2ddb81432d14b4dce82b83b3a95c6d0205';

            public function decrypt(string|array $encryptedData, array $options = []): string|array
            {
                if (is_array($encryptedData)) {
                    array_walk_recursive(
                        $encryptedData,
                        function (mixed &$item, int|string $key) use ($options) {
                            if (is_string($key) && $key === $options['encryptedProperty']) {
                                $item = Crypto::decrypt(
                                    $item,
                                    Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY)
                                );
                            }
                        }
                    );
                    return $encryptedData;
                }
                return Crypto::decrypt(
                    $encryptedData,
                    Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY)
                );
            }

            public function encrypt(string|array $data, array $options = []): string|array
            {
                if (is_array($data)) {
                    array_walk_recursive(
                        $data,
                        function (mixed &$item, int|string $key) use ($options) {
                            if (is_string($key) && $key === $options['encryptedProperty']) {
                                $item = Crypto::encrypt(
                                    $item,
                                    Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY),
                                );
                            }
                        }
                    );
                    return $data;
                }

                return Crypto::encrypt(
                    $data,
                    Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY),
                );
            }
        });
    }
}
