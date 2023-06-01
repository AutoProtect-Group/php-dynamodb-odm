<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\Exception;

use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

class InvalidPropertyTypeException extends DynamoDbAdapterException
{
    public function __construct(
        string $annotationPropertyName,
        array $allowedClassNames,
        string $modelClassName,
        int $code = self::DEFAULT_EXCEPTION_CODE,
    ) {
        $message = sprintf(
            'Type of property %s should be subclass of %s in the class %s',
            $annotationPropertyName,
            implode(", ", $allowedClassNames),
            $modelClassName
        );
        parent::__construct($message, $code);
    }
}
