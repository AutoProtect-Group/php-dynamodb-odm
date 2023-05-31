# AWS Dynamodb ODM for PHP
This is a module to use AWS Dynamo DB SDK in a more convenient way.

## Installation

Run 

```bash
docker-compose build
```
to build a docker image.

Then run `docker-compose run --no-deps dynamo-db-adapter composer install` to install dependencies.

Optionally you may want to set up local env for development. You just need to create a new file ``.env.local`` from  `.env.local.sample` and then set up the vars according to your OS.

## Running tests

### Unit tests

This package uses phpspec for running unit tests.

Run them using the following way:
`docker-compose run --no-deps dynamo-db-adapter vendor/bin/phpspec run`

Please use environment variables in the `.env.local` file to be able to debug the library.

### Functional tests

This package uses behat for running functional tests.
 
Then just run the tests:
 
`docker-compose run dynamo-db-adapter vendor/bin/behat -c behat.yml --stop-on-failure --tags @getItem`

### Syntax check tests

You need to check if the code style is OK by running:
`docker-compose run --no-deps dynamo-db-adapter vendor/bin/phpcs  --basepath=/application/src  --standard=PSR2 src`
