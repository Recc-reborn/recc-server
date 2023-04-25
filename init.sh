#!/usr/bin/env sh

#removes app running in port 5000 if exists
fuser -k 5000/tcp

pip3 install pipenv pandas ansicolor rfc3339 numpy werkzeug

pipenv install --python /usr/bin/python3

pipenv run pip install pandas ansicolor rfc3339 numpy werkzeug

make start-prod
