#!/usr/bin/env sh

cd /app

pip3 install pipenv pandas colors.py rfc3339 numpy werkzeug

pipenv install --python /usr/bin/python3

pipenv run pip install pandas colors.py rfc3339 numpy werkzeug

make start-prod
