up:
	docker compose up --build -d

down:
	docker compose down

fpm-bash:
	docker compose exec php-fpm bash