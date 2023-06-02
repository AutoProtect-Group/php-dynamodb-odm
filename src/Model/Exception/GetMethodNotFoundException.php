<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

/**
 * Class GetMethodNotFoundException
 *
 * @package Autoprotect\DynamodbODM\Model\Exception
 */
class GetMethodNotFoundException extends DynamoDbAdapterException
{
    public const MESSAGE_DEFAULT = 'Get method %s() or %s() are not found for class %s';

    public function __construct(
        string $message,
        int $code = self::DEFAULT_EXCEPTION_CODE,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
