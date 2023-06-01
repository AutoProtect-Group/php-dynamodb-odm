<?php

namespace Autoprotect\DynamodbODM\Model\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

class ModelDiscriminatorException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Model discriminator error';
}
