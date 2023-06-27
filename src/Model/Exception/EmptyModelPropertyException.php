<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

class EmptyModelPropertyException extends DynamoDbAdapterException
{
    protected const MESSAGE_DEFAULT = 'Need to add more properties for %s model';

    public function __construct(
        string $className,
        string $message = self::MESSAGE_DEFAULT,
        int $code = self::DEFAULT_EXCEPTION_CODE,
        Throwable $previous = null
    ) {
        parent::__construct(sprintf($message, $className), $code, $previous);
    }
}
