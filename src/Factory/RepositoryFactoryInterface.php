<?php

namespace Autoprotect\DynamodbODM\Factory;

use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;

/**
 * Interface RepositoryManagerInterface
 *
 * @package Autoprotect\DynamodbODM\Factory
 */
interface RepositoryFactoryInterface
{
    /**
     * Create repository
     *
     * @param string $repositoryClassName
     * @param string $modelClassName
     *
     * @return DynamoDBRepository
     */
    public function create(string $repositoryClassName, string $modelClassName): DynamoDBRepository;
}
