<?php

namespace Autoprotect\DynamodbODM\Repository\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

/**
 * Class GetterDoesNotExist
 *
 * @package Autoprotect\DynamodbODM\Repository\Exception
 */
class GetterDoesNotExistException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Getter doe not exist';
    protected const DEFAULT_EXCEPTION_CODE = 400;
}
