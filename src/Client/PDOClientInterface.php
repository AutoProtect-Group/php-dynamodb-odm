<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Client;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * Interface PDOClientInterface
 *
 * @package Autoprotect\DynamodbODM\Client
 */
interface PDOClientInterface
{
    /**
     * Put item operation
     *
     * @param array $data
     * @return mixed
     */
    public function put(array $data);

    /**
     * Put asynchronously item operation
     *
     * @param array $data
     * @return PromiseInterface
     */
    public function putAsync(array $data): PromiseInterface;

    /**
     * Delete some item from database
     *
     * @param string $id
     * @param array  $data
     *
     * @return mixed
     */
    public function delete(string $id, array $data);

    /**
     * Get some item from database
     *
     * @param string $id
     * @param array  $data
     *
     * @return mixed
     */
    public function get(string $id, array $data);

    /**
     * Update some item in database
     *
     * @param string $id
     * @param array  $data
     *
     * @return mixed
     */
    public function update(string $id, array $data);
}
