<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

/**
 * Class PrimaryKeyException
 *
 * @package Autoprotect\DynamodbODM\Hydrator\Exception
 */
class PrimaryKeyException extends DynamoDbAdapterException
{
    protected const MESSAGE_DEFAULT = 'Primary key does not exist in the given data array for the model %s';

    public function __construct(
        string $modelClassName,
        string $message = self::MESSAGE_DEFAULT,
        int $code = self::DEFAULT_EXCEPTION_CODE,
        ?Throwable $previous = null
    ) {
        parent::__construct(sprintf($message, $modelClassName), $code, $previous);
    }
}
