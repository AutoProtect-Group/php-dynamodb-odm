<?php

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\Collection\CollectionInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;

/**
 * Class GetDocumentCollection
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
class GetDocumentCollection extends AbstractAttributeOperation
{
    /**
     * @var string
     */
    protected string $collectionType;

    /**
     * @param string $collectionType
     *
     * @return $this
     */
    public function withCollectionType(string $collectionType): self
    {
        if (!class_exists($collectionType)) {
            throw new \InvalidArgumentException('Collection type is not found');
        }

        $this->collectionType = $collectionType;

        return $this;
    }

    /**
     * @return CollectionInterface
     *
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function execute(): CollectionInterface
    {
        $item = $this->getDataByProjectionPath();

        $dotOutput = $this->processResponseData($item);

        return $this->hydrateCollection($dotOutput);
    }

    /**
     * @return array
     *
     * @throws EntityNotFoundException
     */
    protected function getDataByProjectionPath(): array
    {
        $query = $this->repositoryContext
            ->getQueryBuilder()
            ->getItem($this->getTableName())
            ->itemKey($this->getItemKey())
            ->setProjections([$this->projectionAttrPath])
            ->getQuery();

        $item = $this->repositoryContext
            ->getClient()
            ->get($this->primaryKey, $query);

        if (!$item) {
            throw new EntityNotFoundException();
        }

        return $item;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws EntityNotFoundException
     */
    protected function processResponseData(array $data): array
    {
        $dotOutput = dot($this->repositoryContext->getMarshaler()->unmarshalItem($data))
            ->get($this->projectionAttrPath);

        if ($dotOutput === null) {
            throw new EntityNotFoundException();
        }

        return $dotOutput;
    }

    /**
     * @param array $items
     *
     * @return CollectionInterface
     * @throws \Exception
     */
    protected function hydrateCollection(array $items): CollectionInterface
    {
        /** @var CollectionInterface $collection **/
        $collection = new $this->collectionType;

        foreach ($items as $modelData) {
            /** @var ModelInterface $model **/
            $model = $this->repositoryContext
                ->getHydrator()
                ->hydrate($modelData);

            $collection->addModel($model);
        }

        return $collection;
    }
}
