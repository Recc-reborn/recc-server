#!/usr/bin/env sh

cd /app

pip install pipenv

pipenv install

make start-prod
