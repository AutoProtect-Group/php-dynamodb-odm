@queryBuilder @removeItemAttribute
Feature: Remove attribute in existing item in DynamoDB
  In order to use dynamo-db-adapter
  As a backend developer
  I need to be able to remove an item attribute by id and projection path

  Scenario: Remove an attribute from existing item by id in DynamoDB table
    Given there is an item in DB:
    """
    {
      "id" : "1",
      "numberProp" : 1,
      "stringProp" : "stringValue",
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "map-type-1",
          "mapProp" : "mapProp"
        }
      },
      "listProp" : [
        "listProp 1",
        "listProp 2"
      ]
    }
    """
    When I send remove item attributes request with key "id" and value "1" with attributes:
    """
      ["numberProp", "listProp", "hashMapProp.map-id-1.mapProp"]
    """
    Then I send a get item request with key "id" and value "1"
    And the marshaled item body must be:
    """
    {
      "id" : "1",
      "stringProp" : "stringValue",
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "map-type-1"
        }
      }
    }
    """
