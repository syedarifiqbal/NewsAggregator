.PHONY: up down build setup migrate fresh seed shell tinker logs test composer-install artisan

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

setup:
	bash setup.sh

migrate:
	docker compose exec app php artisan migrate

fresh:
	docker compose exec app php artisan migrate:fresh --seed

seed:
	docker compose exec app php artisan db:seed

shell:
	docker compose exec app bash

tinker:
	docker compose exec app php artisan tinker

logs:
	docker compose logs -f

test:
	docker compose exec app php artisan test

composer-install:
	docker compose exec app composer install

artisan:
	docker compose exec app php artisan $(cmd)
