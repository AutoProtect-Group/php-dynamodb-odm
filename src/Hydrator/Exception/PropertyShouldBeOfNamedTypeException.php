<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

class PropertyShouldBeOfNamedTypeException extends DynamoDbAdapterException
{
    public function __construct(
        string $message,
        int $code = self::DEFAULT_EXCEPTION_CODE,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
