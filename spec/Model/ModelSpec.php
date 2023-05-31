<?php

namespace spec\Autoprotect\DynamodbODM\Model;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\TestCase;

class ModelSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Model::class);
    }

    function it_implements_model_interface()
    {
        $this->shouldImplement(ModelInterface::class);
    }

    function it_returns_actual_model_table_name()
    {
        putenv('APP_ENV=dev');

        $fakeModel = new class extends Model {
            public const TABLE_NAME = 'fakeModel';
        };

        $actualTableName = $fakeModel::getTableName();
        $expectedTableName = 'fakeModel-dev';

        TestCase::assertEquals($expectedTableName, $actualTableName);
    }

    function it_returns_test_model_table_name()
    {
        putenv('APP_ENV=test');
        putenv('TEST_TABLE_PREFIX=test');

        $fakeModel = new class extends Model {
            public const TABLE_NAME = 'fakeModel';
        };

        $actualTableName = $fakeModel::getTableName();
        $expectedTableName = 'test-fakeModel-test';

        TestCase::assertEquals($expectedTableName, $actualTableName);
    }

    function it_can_access_getters_via_property_calls()
    {
        $fakeModel = new class extends Model {
            public const TABLE_NAME = 'fakeModel';

            protected string $name;

            /**
             * @return string
             */
            public function getName(): string
            {
                return $this->name;
            }

            /**
             * @param string $name
             */
            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $fakeModel->setName('Test name');

        TestCase::assertEquals('Test name', $fakeModel->name);
    }
}
