#!/usr/bin/env bash

set -e

	# inicia o swoole em foreground
	sleep 60
	php /var/www/html/yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations --interactive=0
  php /var/www/html/yii  migrate/up --migrationPath=@yii/rbac/migrations --interactive=0
  php /var/www/html/yii  migrate/up --interactive=0


