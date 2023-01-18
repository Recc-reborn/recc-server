sail = ./vendor/bin/sail

.PHONY: up start stop restart wipe migrate test lfm-artists init ascii

# Docker image management
up:
	$(sail) up -d

stop:
	$(sail) stop

restart:
	$(sail) restart
	cat "recc-reborn-ascii.txt"

wipe:
	$(sail) down --rmi all -v

# Artisan commands
migrate:
	$(sail) artisan migrate

fresh:
	$(sail) artisan db:wipe
	$(sail) artisan migrate:fresh

test:
	$(sail) artisan test

lfm-artists:
	$(sail) artisan lfm:clone-artists

init: start migrate lfm-artists

db:
	$(sail) artisan db

# Really important stuff
ascii-recc:
	cat "recc-ascii.txt"

ascii-recc-reborn:
	cat "recc-reborn-ascii.txt"

start: up ascii-recc

tinker:
	$(sail) artisan tinker

pause:
	$(sail) pause

stop:
	$(sail) stop

kill:
	$(sail) kill

clean:
	$(sail) down
