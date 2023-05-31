<?php

namespace Autoprotect\DynamodbODM\Repository\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

/**
 * Class EntityNotFoundException
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Repository\Exception
 */
class NothingFoundException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Nothing was found';
    protected const DEFAULT_EXCEPTION_CODE = 404;
}
