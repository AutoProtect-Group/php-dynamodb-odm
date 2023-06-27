<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Annotation\Exception\NoPropertyFoundException;
use Autoprotect\DynamodbODM\Query\OperationBuilder\ConsistentReadProperty;
use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;

/**
 * Class AbstractAttributeOperation
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
abstract class AbstractAttributeOperation implements AttributeOperationInterface
{
    use ConsistentReadProperty;

    protected DynamoDBRepository $repositoryContext;
    protected string $primaryKey;
    protected string $projectionAttrPath;

    public function __construct(DynamoDBRepository $repositoryContext)
    {
        $this->repositoryContext = $repositoryContext;
    }

    public function withPrKey(string $primaryKey): static
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function withAttrPath(string $projectionAttrPath): static
    {
        $this->projectionAttrPath = $projectionAttrPath;
        return $this;
    }

    public function withoutConsistentRead(): static
    {
        $this->consistentRead = false;
        return $this;
    }

    public function getTableName(): string
    {
        $modelClassName = $this->repositoryContext
            ->getModelClassName();

        return $modelClassName::getTableName();
    }

    /**
     * @return array
     */
    public function getItemKey(): array
    {
        $prKeyName = $this->repositoryContext
            ->getAnnotationManager()
            ->getPrimaryKeyByModelClassName($this->repositoryContext->getModelClassName())
        ;

        return [$prKeyName => $this->primaryKey];
    }

    abstract public function execute(): mixed;
}
