@main
Feature: DynamoDB Repository logic

  Scenario: Save some record to database and check that itself is saved
    When i save item to database with "Ookdmmzn, Test Customer Name, customer_email@email.com, 99.99" data
    Then i should get "Ookdmmzn, Test Customer Name, customer_email@email.com, 99.99" from database
    Then i should get one "Ookdmmzn, Test Customer Name, customer_email@email.com, 99.99" from database

  Scenario: Upsert of empty map works
    When i upsert the collection "quotations" for "Ookdmmzn, Test Customer Name, customer_email@email.com, 99.99" data
    Then i should get "0" from "quotations" for item with key "Ookdmmzn" from database

  Scenario: Get all items from database
    Given data fixtures:
      | id          | customerName          | customerEmail          | percent |
      | fdkkfhfhyq  | test customer name    | test customer email    | 88.99   |
      | qqeeewwweee | test customer name #2 | test customer email #2 | 88.98   |
      | zzxxfffdddf | test customer name #3 | test customer email #3 | 88.97   |
      | jjhhuiuewq  | test customer name #4 | test customer email #4 | 88.96   |
    Then i should see following items from database:
      | id          | customerName          | customerEmail          | percent |
      | zzxxfffdddf | test customer name #3 | test customer email #3 | 88.97   |
      | jjhhuiuewq  | test customer name #4 | test customer email #4 | 88.96   |
      | qqeeewwweee | test customer name #2 | test customer email #2 | 88.98   |
      | fdkkfhfhyq  | test customer name    | test customer email    | 88.99   |

  Scenario: Get all items from database using index
    Given data fixtures:
      | id          | customerName          | customerEmail          | percent |
      | fdkkfhfhyq  | test customer name #1 | test customer email #1 | 50.00   |
      | qqeeewwweee | test customer name #2 | test customer email #2 | 60.00   |
      | zzxxfffdddf | test customer name #3 | test customer email #3 | 70.00   |
      | jjhhuiuewq  | test customer name #3 | test customer email #3 | 80.00   |
    Then i should see following items from index "dynamo-db-test-index" where primary key is "test customer name #3-test customer email #3":
      | id          | customerName          | customerEmail          | percent |
      | zzxxfffdddf | test customer name #3 | test customer email #3 | 70.00   |
      | jjhhuiuewq  | test customer name #3 | test customer email #3 | 80.00   |

  @key-condition-expressions
  Scenario: Get all items from database using index and key condition expressions
    Given data fixtures:
      | id          | customerName          | customerEmail          | percent |
      | fdkkfhfhyq  | test customer name #1 | test customer email #1 | 50.00   |
      | qqeeewwweee | test customer name #2 | test customer email #2 | 60.00   |
      | zzxxfffdddf | test customer name #3 | test customer email #3 | 70.00   |
      | jjhhuiuewq  | test customer name #3 | test customer email #3 | 80.00   |
    Then i should see following items from index "indexNameEmail-id-index" where primary key is "test customer name #3-test customer email #3" and id begins with "jjh":
      | id          | customerName          | customerEmail          | percent |
      | jjhhuiuewq  | test customer name #3 | test customer email #3 | 80.00   |

  @get-specific-items-from-database
  Scenario: Get specific item from database
    Given data fixtures:
      | id         | customerName       | customerEmail       | percent |
      | fdkkfhfhyq | test customer name | test customer email | 88.99   |
    Then i should get "fdkkfhfhyq, test customer name, test customer email, 88.99" from database

  Scenario: Update specific item
    Given data fixtures:
      | id         | customerName       | customerEmail       | percent |
      | fdkkfhfhyq | test customer name | test customer email | 88.99   |
    When i update item with params "fdkkfhfhyq, test customer name updated, test customer email, 88.99"
    Then i should get "fdkkfhfhyq, test customer name updated, test customer email, 88.99" from database

  @dynamodb-repository-delete-specific-item
  Scenario: Delete specific item
    Given data fixtures:
      | id          | customerName          | customerEmail          | percent |
      | fdkkfhfhyq  | test customer name    | test customer email    | 88.99   |
      | qqeeewwweee | test customer name #2 | test customer email #2 | 88.98   |
    When i delete "fdkkfhfhyq" item
    Then i should see following items from database:
      | id          | customerName          | customerEmail          | percent |
      | qqeeewwweee | test customer name #2 | test customer email #2 | 88.98   |

  @dynamodb-repository-get-items-by-query
  Scenario: Get items by query
    Given data fixtures with sort key:
      | id          | clientId        |
      | BYU78RTY1   | abc-abb-acc-def |
      | ABC0HH5KL   | aaa-abv-vvv-det |
    Then i should see query result from database:
      | id          | clientId        |
      | BYU78RTY1   | abc-abb-acc-def |
      | ABC0HH5KL   | aaa-abv-vvv-det |

  Scenario: Get all items by partition key
    Given data fixtures with sort key:
      | id          | clientId        |
      | ABBA        | abc-abb-acc-def |
      | ABBA        | aaa-abv-vvv-det |
    Then i should see query result by partition key "ABBA" from database:
      | id          | clientId        |
      | ABBA        | abc-abb-acc-def |
      | ABBA        | aaa-abv-vvv-det |

  Scenario: Update existence item by conditions
    Given data fixtures with sort key:
      | id          | clientId        |
      | BYU78RTY1   | abc-abb-acc-def |
    Then i update existence item with params by conditions "BYU78RTY1, abc-abb-acc-def"

  Scenario: Delete existence item by conditions
    Given data fixtures with sort key:
      | id          | clientId        |
      | BYU78RTY1   | abc-abb-acc-def |
    Then i delete existence item with params by conditions "BYU78RTY1, abc-abb-acc-def"

  Scenario: Update non existence item by conditions
    Given data fixtures with sort key:
      | id          | clientId        |
      | BYU78RTY1   | abc-abb-acc-def |
    Then i update non existence item with params by conditions "BYU78RTY9, abc-abb-acc-def"
