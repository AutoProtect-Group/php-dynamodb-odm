<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Encryption;

use Attribute;

/**
 * Class DateType
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Encrypted implements EncryptedPropertyInterface
{
    public function __construct(
        protected array $encryptionOptions = []
    ) {
    }

    public function getOptions(): array
    {
        return $this->encryptionOptions;
    }
}
