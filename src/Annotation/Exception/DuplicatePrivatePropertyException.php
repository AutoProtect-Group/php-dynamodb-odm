<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

class DuplicatePrivatePropertyException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Duplicated Property %s';

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
