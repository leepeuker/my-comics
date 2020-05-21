.PHONY: build

include .env
include .env.local
export

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
app_comics_add:
	make run_cmd_php CMD="php bin/console comic:add $(ID)"

app_image_cleanup_files:
	make run_cmd_php CMD="php bin/console image:cleanup:files"

app_image_cleanup_database:
	make run_cmd_php CMD="php bin/console image:cleanup:database"

app_cache_clear:
	make run_cmd_php CMD="php bin/console cache:clear"

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

db_import:
	docker cp $(FILE) my-comics-mysql:/tmp/dump.sql
	docker exec my-comics-mysql bash -c 'mysql -u${DB_USER} -p${DB_PASSWORD} < /tmp/dump.sql'
	docker exec my-comics-mysql bash -c 'rm /tmp/dump.sql'

db_export:
	docker exec my-comics-mysql bash -c 'mysqldump --databases --add-drop-database -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) > /tmp/dump.sql'
	docker cp my-comics-mysql:/tmp/dump.sql var/my-comics-`date +%Y-%m-%d-%H-%M-%S`.sql
	docker exec my-comics-mysql bash -c 'rm /tmp/dump.sql'

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
