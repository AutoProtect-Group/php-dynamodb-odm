<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

class PropertyNotFoundException extends DynamoDbAdapterException
{
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Property was not found';
    protected const DEFAULT_EXCEPTION_CODE = 404;
}
