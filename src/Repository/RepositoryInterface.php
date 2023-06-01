<?php

namespace Autoprotect\DynamodbODM\Repository;

use Autoprotect\DynamodbODM\Model\ModelInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Interface RepositoryInterface
 *
 * @package Autoprotect\DynamodbODM\Repository
 */
interface RepositoryInterface
{
    /**
     * Saves the model
     *
     * @param ModelInterface $model
     *
     * @return ModelInterface
     */
    public function save(ModelInterface $model): ModelInterface;

    /**
     * Saves the model asynchronously
     *
     * @param ModelInterface $model
     * @return mixed
     */
    public function saveAsync(ModelInterface $model): PromiseInterface;

    /**
     * Saves the model synchronously by conditions
     */
    public function saveByConditions(ModelInterface $model): ?ModelInterface;

    /**
     * Get the model by ID from DB
     *
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function get(string $id): ?ModelInterface;

    /**
     * Update the model
     *
     * @param string $id
     * @param array  $updateParams
     *
     * @return mixed
     */
    public function update(string $id, array $updateParams);

    /**
     * Get all records from specific table
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Delete the model from DB.
     *
     * @param ModelInterface $model
     *
     * @return ModelInterface
     */
    public function delete(ModelInterface $model): ModelInterface;
}
