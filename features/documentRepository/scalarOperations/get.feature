@get-document-projection-property @repository
Feature: Get document and get scalar properties from the database by projection
  In order to get a projected document scalar property
  I need to use the document repository

  Background:
    Given data nested fixtures:
      | id         | name      | childObject_NESTED                                                                                            |
      | theId      | test name | {"id": "asdasd", "customerName": "customer full name", "customerEmail": "asdasd@asfas.com", "percent": 23.09} |

  @get-existent-document-projection-property
  Scenario: Get property by item ID and projection expression
    When I send a get document property request with the key value "theId" and expression "childObject.customerName"
    Then I should see the property with the value "customer full name"

  @get-existent-document-non-existent-projection-property
  Scenario: Get property by item ID and projection expression
    When I send a get document property request with the key value "theId" and expression "childObject.NonExistentCustomerName"
    Then I should get a PropertyNotFoundException exception

  @get-non-existent-document-projection-property
  Scenario: Get property by item ID and projection expression
    When I send a get document property request with the key value "nonExistentId" and expression "childObject.NonExistentCustomerName"
    Then I should get a EntityNotFoundException exception
