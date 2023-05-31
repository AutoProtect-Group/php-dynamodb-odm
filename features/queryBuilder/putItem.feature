@queryBuilder @putItem
Feature: Put an item in the DynamoDB
  In order to use dynamo-db-adapter
  As a backend developer
  I need to be able to put an item in the DynamoDB table

  Scenario: Put item in DynamoDB table
    When I send a put item request with body:
    """
    {
      "id" : "1",
      "numberProp" : 1,
      "stringProp" : "stringValue"
    }
    """
    When I send a get item request with key "id" and value "1"
    Then the un marshaled item body must be:
    """
    {
      "id" : {
        "S" : "1"
      },
      "numberProp" : {
        "N" : 1
      },
      "stringProp" : {
        "S" : "stringValue"
      }
    }
    """
    And the marshaled item body must be:
    """
    {
      "id" : "1",
      "numberProp" : 1,
      "stringProp" : "stringValue"
    }
    """