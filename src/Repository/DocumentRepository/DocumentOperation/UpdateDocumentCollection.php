<?php

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;

/**
 * Class UpdateDocumentCollection
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Repository\DocumentRepository\DocumentOperation
 */
class UpdateDocumentCollection extends AbstractAttributeOperation
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
    public function withCollection(array $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function execute(): array
    {
        $query = $this->repositoryContext
            ->getQueryBuilder()
            ->updateItem($this->getTableName())
            ->itemKey($this->getItemKey())
            ->attributes([
                $this->projectionAttrPath => $this->serializeCollection()
            ])
            ->getQuery();

        $item = $this->repositoryContext
            ->getClient()
            ->update($this->primaryKey, $query);

        if (!$item) {
            throw new EntityNotFoundException();
        }

        $dotOutput = dot($this->repositoryContext->getMarshaler()->unmarshalItem($item))
            ->get($this->projectionAttrPath);

        return $this->collection;
    }

    /**
     * @return array
     */
    private function serializeCollection()
    {
        $serializer = $this->repositoryContext->getSerializer();

        return array_map(static function (ModelInterface $model) use ($serializer) {
            return $serializer->serialize($model);
        }, $this->collection);
    }
}
