.PHONY: start-dev start-prod

start-dev: app.py
	pipenv run flask run

start-prod: app.prod.py
	pipenv run flask run
