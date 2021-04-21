# Sezane 

## Requirements
- docker
- docker-compose
- make

**Make sure you have nothing launched on ports 80, 3306, 9200 and 5601 !**

## Installation
Short story
```bash 
make install
```

Long Story
```bash
docker-compose up -d --build
docker-compose exec php composer install --no-interaction -o
docker-compose exec php bin/console doctrine:database:drop --if-exists --force
docker-compose exec php bin/console doctrine:database:create --if-not-exists
docker-compose exec php bin/console doctrine:schema:update --force --no-interaction
docker-compose exec php bin/console doctrine:migrations:version --add --all --no-interaction
docker-compose exec php bin/console doctrine:fixtures:load --no-interaction
docker-compose exec php bin/console app:elastic:populate
docker-compose exec php chmod -R 777 var/
```

You can now access the website at http://localhost/api/doc

## Tests

Launch all tests
```bash 
make test
```

Launch only unit tests
```bash 
make tu
```

Launch only functional tests
```bash 
make tf
```
