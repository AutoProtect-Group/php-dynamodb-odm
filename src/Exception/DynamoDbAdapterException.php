<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Exception;

use RuntimeException;
use Throwable;

abstract class DynamoDbAdapterException extends RuntimeException
{
    public const DYNAMODB_EXCEPTION_TRANSACTION_CANCELLED = 'TransactionCanceledException';
    public const DYNAMODB_EXCEPTION_RESOURCE_NOT_FOUND = 'ResourceNotFoundException';
    public const DYNAMODB_EXCEPTION_TRANSACTION_IN_PROGRESS = 'TransactionInProgressException';
    public const DYNAMODB_EXCEPTION_TABLE_ALREADY_EXISTS = 'TableAlreadyExistsException';

    /**
     * Also thrown when the table already exists
     */
    public const DYNAMODB_EXCEPTION_RESOURCE_IN_USE = 'ResourceInUseException';
    protected const DEFAULT_EXCEPTION_MESSAGE = 'Undefined general dynamo db adapter error';
    protected const DEFAULT_EXCEPTION_CODE = 500;

    public function __construct(
        ?string $message = null,
        ?int $code = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $message ?? static::DEFAULT_EXCEPTION_MESSAGE,
            $code ?? static::DEFAULT_EXCEPTION_CODE,
            $previous
        );
    }
}
