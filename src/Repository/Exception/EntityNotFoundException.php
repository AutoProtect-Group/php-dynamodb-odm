<?php

namespace Autoprotect\DynamodbODM\Repository\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

/**
 * Class EntityNotFoundException
 *
 * @package Autoprotect\DynamodbODM\Repository\Exception
 */
class EntityNotFoundException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Entity not found';
    protected const DEFAULT_EXCEPTION_CODE = 404;
}
