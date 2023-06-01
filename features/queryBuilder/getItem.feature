@queryBuilder @getItem
Feature: Get item from DynamoDB
  In order to use dynamo-db-adapter
  As a backend developer
  I need to be able to get item by id from DynamoDB table

  Background:
    Given there is an item in DB:
    """
    {
      "id" : "1",
      "numberProp" : 1,
      "stringProp" : "stringValue",
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "map-type-1"
        }
      }
    }
    """

  Scenario: Get item by id and expression from DynamoDB table
    When I send a get item request with key "id" and value "1" with expression "hashMapProp.map-id-1"
    And the marshaled item body must be:
    """
    {
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "map-type-1"
        }
      }
    }
    """

  Scenario: Get item by id from DynamoDB table
    When I send a get item request with key "id" and value "1" with expression "nonExistObjectProp"
    And the marshaled item body must be:
    """
    {}
    """
  @get-item-by-id
  Scenario: Get item by id from DynamoDB table
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
      },
      "hashMapProp" : {
        "M" : {
          "map-id-1" : {
            "M" : {
              "id" : {
                "S" : "map-id-1"
              },
              "type" : {
                "S" : "map-type-1"
              }
            }
          }
        }
      }
    }
    """
    And the marshaled item body must be:
    """
    {
      "id" : "1",
      "numberProp" : 1,
      "stringProp" : "stringValue",
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "map-type-1"
        }
      }
    }
    """