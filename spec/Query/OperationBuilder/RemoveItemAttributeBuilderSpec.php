<?php

namespace spec\Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\OperationBuilder\RemoveItemAttributeBuilder;
use PhpSpec\ObjectBehavior;

/**
 * Class RemoveItemAttributeBuilderSpec
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Query\OperationBuilder
 */
class RemoveItemAttributeBuilderSpec extends ObjectBehavior
{
    private string $testTable = 'test-TableName';

    public function let(): void
    {
        $marshaller = new Marshaler();
        $this->beConstructedWith($marshaller, $this->testTable);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveItemAttributeBuilder::class);
    }

    public function getExpectedUpdateQuery(): array
    {
        return [
            'TableName' => $this->testTable,
            'ExpressionAttributeNames' => [
                '#1303c06b0b01' => 'type',
                '#fd54e5a82e36' => 'applications',
                '#7db027dc1885' => 'applicationId',
                '#76daa138e9e1' => 'applicants',
                '#d0cf187f4e4d' => 'applicantId',
            ],
            'Key' => [
                'id' => [
                    'S' => '1',
                ],
            ],
            'ReturnValues' => 'ALL_NEW',
            'UpdateExpression' => 'REMOVE #1303c06b0b01, #fd54e5a82e36.#7db027dc1885.#76daa138e9e1.#d0cf187f4e4d',
        ];
    }

    public function it_can_build_update_expression(): void
    {
        $this
            ->itemKey(['id' => '1'])
            ->removeAttributesByPath([
                'type',
                'applications.applicationId.applicants.applicantId'
            ])
            ->getQuery()
            ->shouldBeLike($this->getExpectedUpdateQuery());
    }
}
