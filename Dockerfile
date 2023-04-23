FROM python:3-alpine
WORKDIR /app
ENV FLASK_APP=app.py
ENV FLASK_RUN_HOST=0.0.0.0
COPY . /app
RUN apk update && apk add --virtual build-dependencies build-base gcc
RUN pip install --upgrade pip
RUN pip3 install --upgrade pip
RUN pip install pipenv pandas colors.py rfc3339 numpy werkzeug
RUN pipenv install --python /usr/local/bin/python3
RUN pipenv run pip install pandas colors.py rfc3339 numpy werkzeug
EXPOSE 5000
CMD ["pipenv", "run", "python", "app.py"]
