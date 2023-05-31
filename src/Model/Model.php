<?php

namespace Autoprotect\DynamodbODM\Model;

use Autoprotect\DynamodbODM\Model\Exception\GetMethodNotFoundException;
use Autoprotect\DynamodbODM\Model\Exception\PropertyGetterIsNotFound;

/**
 * Class Model
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Model
 */
class Model implements ModelInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName(): string
    {
        $currentApplicationEnvironment = getenv('APP_ENV', true);

        $tableName = !empty($currentApplicationEnvironment)
            ? static::TABLE_NAME . '-' . $currentApplicationEnvironment
            : static::TABLE_NAME;

        $currentTestTablePrefix = getenv('TEST_TABLE_PREFIX', true);

        return !empty($currentTestTablePrefix)
            ? $currentTestTablePrefix . '-' . $tableName
            : $tableName;
    }

    /**
     * This is useful for accessing fields using their getters
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        $getterMethod = 'get' . ucfirst($name);

        if (method_exists($this, $getterMethod)) {
            return $this->$getterMethod();
        }

        $getterMethod = 'is' . ucfirst($name);

        if (method_exists($this, $getterMethod)) {
            return $this->$getterMethod();
        }

        throw new PropertyGetterIsNotFound(sprintf('Property "%s" getter function is not found', $name));
    }
}
