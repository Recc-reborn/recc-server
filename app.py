from werkzeug.exceptions import HTTPException
from flask import Flask, jsonify, request
from dotenv import load_dotenv
import pandas as pd
import numpy as np
import json
import random

app = Flask(__name__)

@app.errorhandler(HTTPException)
def handle_exception(e):
    """Return JSON instead of HTML for HTTP errors."""
    # start with the correct headers and status code from the error
    response = e.get_response()
    # replace the body with JSON
    response.data = json.dumps({
        "code": e.code,
        "name": e.name,
        "description": e.description,
    })
    response.content_type = "application/json"
    return response


@app.route("/")
def greet():
    return "Hello :)"


@app.route("/create_playlist", methods=["GET"])
def create_playlist():
    if request.method != "GET":
        app.logger.warning("Unsoported method call")
        raise HTTPException("Unsoported request method")

    response = {
        "data": [random.randint(0, 1000) for _ in range(10)]
    }
    # For now return indexes between 0 and 1000, we can used as they are the songs id
    return jsonify(response)


@app.route("/test", methods=['GET'])
def demo():
    if request.method != "GET":
        app.logger.warning("Unsoported method call")
        raise HTTPException("Unsoported request method")

    response = query_all_tracks(connection)

    return jsonify([dict(dict_row) for dict_row in response.mappings()])
