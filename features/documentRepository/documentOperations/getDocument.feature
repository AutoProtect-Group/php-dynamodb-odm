@get-document @repository
Feature: Get document from the database by projection
  In order to get a projected document
  I need to use the document repository

  Background:
    Given data nested fixtures:
      | id         | name      | childObject_NESTED                                                                                            |
      | theId      | test name | {"id": "asdasd", "customerName": "customer full name", "customerEmail": "asdasd@asfas.com", "percent": 23.09} |

  @get-existent-document-existent-child
  Scenario: Get document by item ID and projection expression
    When I send a get document request with the key value "theId" and expression "childObject"
    Then I should see the object coming from database


  @get-existent-document-non-existent-child
  Scenario: Get document by item ID and projection expression
    When I send a get document request with the key value "theId" and expression "nonExistentchildObject"
    Then I should get a EntityNotFoundException exception

  @get-non-existent-document-non-existent-child
  Scenario: Get document by item ID and projection expression
    When I send a get document request with the key value "theId" and expression "nonExistentchildObject"
    Then I should get a EntityNotFoundException exception
