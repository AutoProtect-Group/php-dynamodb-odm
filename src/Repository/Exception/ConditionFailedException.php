<?php

namespace Autoprotect\DynamodbODM\Repository\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

class ConditionFailedException extends DynamoDbAdapterException
{
    public const AWS_ERROR_CODE = 'ConditionalCheckFailedException';
    protected const DEFAULT_EXCEPTION_MESSAGE = 'The conditional request failed';
    protected const DEFAULT_EXCEPTION_CODE = 400;
}
