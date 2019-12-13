include .env
include .env.local

init:
	cp .env.dist .env
	cp phinx.yml.dist phinx.yml

# Docker
########
build:
	docker-compose build --no-cache --build-arg USER_ID=${USER_ID}

up:
	docker-compose up -d

down:
	docker-compose down

reup: down up

connect_php_bash:
	docker exec -it my-comics-php bash

connect_nginx_bash:
	docker exec -it my-comics-nginx bash

connect_mysql_cli:
	docker exec -it my-comics-mysql bash -c "mysql -uroot -p${MYSQL_ROOT_PASSWORD}"

run_cmd_php:
	docker exec -i my-comics-php bash -c "${CMD}"

run_cmd_mysql:
	docker exec -it my-comics-mysql bash -c "mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e \"$(QUERY)\""

reload_nginx:
	docker exec -it my-comics-nginx bash -c "service nginx reload"

# App
#####
app_add_comic:
	make run_cmd_php CMD="php bin/console comic:add $(ID)"

app_add_comic_fixtures:
	make run_cmd_php CMD="php bin/console comic:add 522540"
	make run_cmd_php CMD="php bin/console comic:add 562618"
	make run_cmd_php CMD="php bin/console comic:add 499800"
	make run_cmd_php CMD="php bin/console comic:add 537968"

# Database
##########
db_create_database:
	make run_cmd_mysql QUERY="DROP DATABASE IF EXISTS $(DB_NAME)"
	make run_cmd_mysql QUERY="CREATE DATABASE $(DB_NAME)"
	make run_cmd_mysql QUERY="GRANT ALL PRIVILEGES ON $(DB_NAME).* TO 'dev'@'%'"
	make run_cmd_mysql QUERY="FLUSH PRIVILEGES;"
	make db_migration_migrate

db_migration_migrate:
	make run_cmd_php CMD="vendor/bin/phinx $(PHINX) migrate --configuration ./phinx.yml -e $(APP_ENV)"

db_migration_rollback:
	make run_cmd_php CMD="vendor/bin/phinx rollback --configuration ./phinx.yml -e $(APP_ENV)"

db_migration_create:
	make run_cmd_php CMD="vendor/bin/phinx create Migration$(shell date +%s) --configuration ./phinx.yml"

db_seed_run:
	make run_cmd_php CMD="vendor/bin/phinx seed:run"

db_seed_create:
	make run_cmd_php CMD="vendor/bin/phinx seed:create DefaultSeeder"

# Composer
#########
composer_install:
	make run_cmd_php CMD="composer install"

composer_update:
	make run_cmd_php CMD="composer update"

# Tests
#######
test: test_phpstan test_psalm test_phpunit

test_phpunit:
	make run_cmd_php CMD="vendor/bin/phpunit -c ./phpunit.xml --testsuite unit"

test_phpstan:
	make run_cmd_php CMD="vendor/bin/phpstan analyse src tests/unit --level max"

test_psalm:
	make run_cmd_php CMD="vendor/bin/psalm -c ./psalm.xml --show-info=false"

test_psalm_with_info:
	make run_cmd_php CMD="vendor/bin/psalm -c ./psalm.xml --show-info=true"
