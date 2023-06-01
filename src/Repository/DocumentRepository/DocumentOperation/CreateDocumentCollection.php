<?php

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\Collection\CollectionInterface;
use Autoprotect\DynamodbODM\Model\Collection\HashMap;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;

/**
 * Class CreateDocumentCollection
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
class CreateDocumentCollection extends GetDocumentCollection
{
    /**
     * @var array
     */
    protected array $collection;

    /**
     * @param array $collection
     *
     * @return $this
     */
    public function withCollectionData(array $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return CollectionInterface
     *
     * @throws EntityNotFoundException
     * @throws ExpressionNotFoundException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function execute(): CollectionInterface
    {
        $query = $this->repositoryContext
            ->getQueryBuilder()
            ->updateItem($this->getTableName())
            ->itemKey($this->getItemKey())
            ->attributes([
                $this->projectionAttrPath => $this->serializeCollection()
            ])
            ->getQuery();

        $items = $this->repositoryContext
            ->getClient()
            ->update($this->primaryKey, $query);

        if (!$items) {
            throw new EntityNotFoundException();
        }

        $dotOutput = $this->processResponseData($items);

        return $this->hydrateCollection($dotOutput);
    }

    /**
     * @return array
     */
    private function serializeCollection(): array
    {
        $serializer = $this->repositoryContext->getSerializer();

        if ($this->collectionType === HashMap::class) {
            $processedValue = [];

            foreach ($this->collection as $key => $model) {
                $processedValue[$model->getId()] = $serializer->serialize($model);
            }

            return $processedValue;
        }

        return array_map(static function (ModelInterface $model) use ($serializer) {
            return $serializer->serialize($model);
        }, $this->collection);
    }
}
