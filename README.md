# Publishers book 2023

Project on Symfony 6 using docker.

## Install project

### Things you need
* composer
* npm
* docker
* php-cs-fixer (brew install php-cs-fixer)

### Clone repository to your local machine
```bash
git clone git@github.com:moroztaras/publishers-book.git
```

### Create project config
```bash
cd publishers-book
cp .env .env.local
cp ./docker/.env.dist ./docker/.env
```
### Quick start of the project

Adjust .env.local.

It's credentials to database.

### Run a project with the docker
```bash
docker-compose -f docker/docker-compose.yaml up -d
```

### Execute a migration to the latest available version
```bash
./bin/console doctrine:migrations:migrate
```

### Load data fixtures to database
```bash
./bin/console doctrine:fixtures:load
```

### Run server
```bash
symfony serve:start
```

### Go to the link at
```bash
http://127.0.0.1:8000/api/doc
```

### Run tests
```bash
docker-compose -f docker/docker-compose.test.yaml up -d
```
```bash
php bin/console doctrine:migrations:migrate -e test
```
```bash
php bin/phpunit
```

© 2023
