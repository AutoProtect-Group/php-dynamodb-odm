<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

class NoEncryptionOptionsDefinedForProperty extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = "No encryption options defined for the property %s";

    public function __construct(
        string $propertyName,
        ?string $message = null,
        ?int $code = null,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            $message ?? sprintf(static::DEFAULT_EXCEPTION_MESSAGE, $propertyName),
            $code,
            $previous
        );
    }
}
