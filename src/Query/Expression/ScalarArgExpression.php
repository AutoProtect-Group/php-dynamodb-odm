<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

use Aws\DynamoDb\Marshaler;

abstract class ScalarArgExpression extends AbstractExpression
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected string $paramName;

    public function __construct(Marshaler $marshaler)
    {
        $this->initializeParamValue();

        parent::__construct($marshaler);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->marshaler->marshalItem([':' . $this->paramName => $this->value]);
    }

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->key, $this->paramName);
    }

    /**
     * @return string
     */
    public function getParamName(): string
    {
        return $this->paramName;
    }

    /**
     * Get initial value that was set for the object
     *
     * @return mixed
     */
    public function getInitialValue()
    {
        return $this->value;
    }

    /**
     * Initialize param value for the scalar expresion
     */
    protected function initializeParamValue(): void
    {
        $this->paramName = uniqid('', false);
    }

    /**
     * @return string
     */
    protected function getKeyHash(): string
    {
        return self::getSha256Hash($this->key);
    }

    /**
     * @return string
     */
    protected function getParamNameHash(): string
    {
        return self::getSha256Hash($this->paramName);
    }

    /**
     * @return string
     */
    protected function getParamValueHash(): string
    {
        return self::getSha256Hash($this->getInitialValue());
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function getSha256Hash($value): string
    {
        return substr(hash('sha256', $value), 0, 12);
    }
}
