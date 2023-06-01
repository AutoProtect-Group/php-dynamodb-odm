<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Encryptor;

interface EncryptorInterface
{
    /**
     * Encrypt string or scalar array data with custom encryptor.
     * Options are for any kind of settings for current property.
     *
     * WARNING: In case of array is given then it's recommended to pass specific options to the encryptor in order to
     * show the way to how encrypt the data
     *
     *
     *
     * @param string|array $data
     * @param array        $options
     *
     * @return string|array
     */
    public function encrypt(string|array $data, array $options = []): string|array;

    /**
     * Decrypt the property. Options are also for any kind of specific settings for current property
     * on how it should be decrypted.
     *
     * WARNING: In case of array is given then it's recommended to pass specific options to the encryptor in order to
     * show the way to how decrypt the data
     *
     * @param string|array $encryptedData
     * @param array        $options
     *
     * @return string|array
     */
    public function decrypt(string|array $encryptedData, array $options = []): string|array;
}
