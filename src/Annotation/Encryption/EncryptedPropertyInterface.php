<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Encryption;

interface EncryptedPropertyInterface
{
    /**
     * Get various options for current field encryption. Options will be passed into the encryptor
     *
     * @return array
     */
    public function getOptions(): array;
}
