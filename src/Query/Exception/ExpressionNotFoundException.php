<?php

namespace Autoprotect\DynamodbODM\Query\Exception;

/**
 * Class ExpressionNotFoundException
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Exception
 */
class ExpressionNotFoundException extends QueryBuilderException
{
    /**
     * @var string
     */
    protected $messageTemplate = 'Expression "%s" not found';

    /**
     * ExpressionNotFoundException constructor.
     *
     * @param string         $className
     * @param int            $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $className, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf($this->messageTemplate, $className), $code, $previous);
    }
}
