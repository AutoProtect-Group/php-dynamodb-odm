<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

class NoPropertyFoundException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'No property found by the given name %s';

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
