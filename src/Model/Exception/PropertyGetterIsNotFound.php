<?php

namespace Autoprotect\DynamodbODM\Model\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

class PropertyGetterIsNotFound extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Property getter is not found';
}
