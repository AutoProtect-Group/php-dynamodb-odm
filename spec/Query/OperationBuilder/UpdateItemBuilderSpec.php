<?php

namespace spec\Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\OperationBuilder\UpdateItemBuilder;
use PhpSpec\ObjectBehavior;

/**
 * Class UpdateItemBuilderSpec
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Query\OperationBuilder
 */
class UpdateItemBuilderSpec extends ObjectBehavior
{
    private $testTable = 'test-TableName';

    public function let(): void
    {
        $marshaller = new Marshaler();
        $this->beConstructedWith($marshaller, $this->testTable);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateItemBuilder::class);
    }

    public function getExpectedUpdateQuery(): array
    {
        return [
            'TableName' => $this->testTable,
            'ExpressionAttributeNames' => [
                '#1303c06b0b01' => 'type',
                '#fd54e5a82e36' => 'applications',
            ],
            'ExpressionAttributeValues' => [
                ':35eede9a998c' => [
                    'S' => 'Business',
                ],
                ':5a9f7f9b740d' => [
                    'L' => [],
                ]
            ],
            'Key' => [
                'id' => [
                    'S' => '1',
                ],
            ],
            'ReturnValues' => 'ALL_NEW',
            'UpdateExpression' => 'SET #1303c06b0b01 = :35eede9a998c, #fd54e5a82e36 = :5a9f7f9b740d',
        ];
    }

    public function it_can_build_update_expression(): void
    {
        $this
            ->itemKey(['id' => '1'])
            ->attributes([
                'type' => 'Business',
                'applications' => []
            ])
            ->getQuery()
            ->shouldBeLike($this->getExpectedUpdateQuery());
    }
}
