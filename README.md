# AWS Dynamodb ODM for PHP

![Code style, unit and functional tests](https://github.com/AutoProtect-Group/php-dynamodb-odm/actions/workflows/ci.yml/badge.svg)

This is a library and an Object Document Mapper to use with AWS DynamoDB in a more convenient way.

## Usage

To be added soon...

## Local dev environment installation

1. In order to build a dev image, please, run: 
```bash
docker-compose build
```
2. Then run to install dependencies: 
```bash
docker-compose run --no-deps dynamodb-odm composer install
```

## Running tests

### Unit tests

This package uses phpspec for running unit tests.

Run them using the following way:
`docker-compose run --no-deps dynamodb-odm vendor/bin/phpspec run`

One can use environment variables in the `.env.local` file to be able to debug the library. For this just Copy file [.env.local.sample](.env.local.sample) into [.env.local](.env.local) and set up the variable according to your OS.

And then run the tests with:

```bash
docker-compose --env-file ./.env.local run  --no-deps dynamodb-odm vendor/bin/phpspec run
```

### Functional tests

This package uses behat for running functional tests.
 
Then just run the tests:
 
`docker-compose run dynamodb-odm vendor/bin/behat -c behat.yml --stop-on-failure`

### Syntax check tests

You need to check if the code style is OK by running:
`docker-compose run --no-deps dynamodb-odm vendor/bin/phpcs  --basepath=/application/src  --standard=PSR2 src`
