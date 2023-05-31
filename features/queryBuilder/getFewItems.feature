@queryBuilder @getFewItems
Feature: Get few items from DynamoDB
  In order to use dynamo-db-adapter
  As a backend developer
  I need to be able to get few items from DynamoDB table

  @get-items-from-the-table
  Scenario: Get items from DynamoDB table
    Given there are the items in DB:
    """
    [
      {
        "id" : "1",
        "numberProp" : 1,
        "stringProp" : "stringValue"
      },
      {
        "id" : "2",
        "numberProp" : 2,
        "stringProp" : "stringValue"
      },
      {
        "id" : "3",
        "numberProp" : 3,
        "stringProp" : "stringValue"
      },
      {
        "id" : "4",
        "numberProp" : 4,
        "stringProp" : "stringValue"
      },
      {
        "id" : "5",
        "numberProp" : 5,
        "stringProp" : "stringValue"
      }
    ]
    """
    When I send get items request with limit "3"
    And there should be not more than "3" items