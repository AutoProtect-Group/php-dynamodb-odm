<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

class EncryptionTypeNotSupportedException extends DynamoDbAdapterException
{
}
