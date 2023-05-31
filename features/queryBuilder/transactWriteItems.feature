@queryBuilder @transactWriteItems
Feature: Transact write items in the DynamoDB
  In order to use dynamo-db-adapter
  As a backend developer
  I need to be able to transact write items in the DynamoDB

  Scenario: Transact write in DynamoDB table
    Given there is an item in DB:
    """
    {
      "id" : "1",
      "numberProp" : 1,
      "stringProp" : "stringValue"
    }
    """
    When I send a transact write items request with body:
    """
    {
      "id": "1",
      "numberProp" : 1,
      "stringProp" : "stringValue2"
    }
    """
    When I send a get item request with key "id" and value "1"
    Then the un marshaled item body must be:
    """
    {
        "stringProp": {
            "S": "stringValue2"
        },
        "id": {
            "S": "1"
        },
        "numberProp": {
            "N": "1"
        }
    }
    """
    And the marshaled item body must be:
    """
    {
        "stringProp": "stringValue2",
        "id": "1",
        "numberProp": 1
    }
    """