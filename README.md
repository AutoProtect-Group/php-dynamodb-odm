# AWS Dynamodb ODM for PHP
This is a module to use AWS Dynamo DB SDK in a more convenient way.

## Local dev environment installation

1. Copy file [.env.local.sample](.env.local.sample) into [.env.local](.env.local) and set up the variable according to your OS
2. In order to build a dev image, please, run: 
```bash
docker-compose build
```
3. Then run to install dependencies: 
```bash
docker-compose run --no-deps dynamo-db-adapter composer install
```

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
