@queryBuilder @updateItem
Feature: Update existing item in DynamoDB
  In order to use dynamo-db-adapter
  As a backend developer
  I need to be able to update an item by id in DynamoDB table

  Scenario: Update an existing item by id in DynamoDB table
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
    When I send update item request with key "id" and value "1" with attributes:
    """
    {
      "numberProp" : 2,
      "stringProp" : "updated string value",
      "hashMapProp.map-id-1.type" : "updated map-type-1",
      "hashMapProp.map-id-1.mapProp" : "updated mapProp",
      "listProp" : [
        "updated listProp 1",
        "updated listProp 2"
      ]
    }
    """
    Then I send a get item request with key "id" and value "1"
    And the marshaled item body must be:
    """
    {
      "id" : "1",
      "numberProp" : 2,
      "stringProp" : "updated string value",
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "updated map-type-1",
          "mapProp" : "updated mapProp"
        }
      },
      "listProp" : [
        "updated listProp 1",
        "updated listProp 2"
      ]
    }
    """

    # Then append two items into the list field
    When I send append items request with key "id" and value "1" with attributes:
    """
    {
      "listProp" :  [
              "updated listProp 4",
              "updated listProp 3"
            ]
    }
    """
    Then I send a get item request with key "id" and value "1"
    And the marshaled item body must be:
    """
    {
      "id" : "1",
      "numberProp" : 2,
      "stringProp" : "updated string value",
      "hashMapProp" : {
        "map-id-1" : {
          "id" : "map-id-1",
          "type" : "updated map-type-1",
          "mapProp" : "updated mapProp"
        }
      },
      "listProp" : [
        "updated listProp 4",
        "updated listProp 3",
        "updated listProp 1",
        "updated listProp 2"
      ]
    }
    """
