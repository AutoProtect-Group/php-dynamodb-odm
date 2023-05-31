<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;
use Throwable;

/**
 * Class PropertySetterMethodDoesNotExistException
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Hydrator\Exception
 */
class PropertySetterMethodDoesNotExistException extends DynamoDbAdapterException
{
    protected const MESSAGE_DEFAULT = 'Property setter method does not exist for class %s';

    public function __construct(
        string $className,
        string $message = self::MESSAGE_DEFAULT,
        int $code = self::DEFAULT_EXCEPTION_CODE,
        ?Throwable $previous = null
    ) {
        parent::__construct(sprintf($message, $className), $code, $previous);
    }
}
