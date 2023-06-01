<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

/**
 * Class IdSetterDoesNotExistException
 *
 * @package Autoprotect\DynamodbODM\Hydrator\Exception
 */
class IdSetterDoesNotExistException extends DynamoDbAdapterException
{
    protected const MESSAGE_DEFAULT = 'Primary model ID setter does not exist for class %s';

    public function __construct(
        string $className,
        string $message = self::MESSAGE_DEFAULT,
        int $code = self::DEFAULT_EXCEPTION_CODE,
        ?Throwable $previous = null
    ) {
        parent::__construct(sprintf($message, $className), $code, $previous);
    }
}
