# Joke Test

## Prerequisites

 - Docker
 - Docker Compose

## Getting Started
Run the commands in order below

    docker-compose up -d
    docker-compose exec app composer install
    docker-compose run --rm node npm i
    docker-compose run --rm node npm run build
	docker-compose exec app php bin/console doctrine:migrations:migrate

Navigate to [http://localhost:8888/joke](http://localhost:8888/joke)
