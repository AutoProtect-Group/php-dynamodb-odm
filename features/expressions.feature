@expressions
Feature: DynamoDB Specific Expressions

  Scenario: Get specific item from database
    Given data nested fixtures:
      | id         | name      | childObject_NESTED                                                                                  |
      | fdkkfhfhyq | test name | {"id": "asdasd", "customerName": "sub name", "customerEmail": "asdasd@asfas.com", "percent": 23.09} |
    Then using projection expressions I should see following items from database:
      | id          | projectionExpression      | modelData                                                                                           |
      | fdkkfhfhyq  | childObject               | {"id": "asdasd", "customerName": "sub name", "customerEmail": "asdasd@asfas.com", "percent": 23.09} |



