.PHONY: start-dev start-prod

start-dev:
	pipenv run flask run

start-prod:
	pipenv run python app.prod.py
