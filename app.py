from werkzeug.exceptions import HTTPException
from flask import Flask, jsonify, request, g, make_response
import pandas as pd
import numpy as np
import json
import datetime
import time
import colors
from rfc3339 import rfc3339
import logging
from ResponseClass import Good_Response, Bad_Response
import db.db_engine as DB

db_instance = DB.create_instance()
learning_matrix = pd.read_csv('./recomendation_IA.csv', index_col=0)
valid_songs = learning_matrix.index.to_list()
app = Flask(__name__)
logging.basicConfig(level=logging.DEBUG)
print('App running...')

# Services
def is_valid_id(id: int) -> bool:
    return id in valid_songs


def recomend_a_song(id: str, amount_songs: int = 10) -> list[int]:
    if (not is_valid_id(int(id))):
        return []
    return learning_matrix[id].sort_values().index[1:amount_songs + 1].to_list()


def recomend_multiple_songs(ids: list[str], amount_songs: int = 10):
    recodantions_per_song = []
    for id in ids:
        recodantions_per_song.append(recomend_a_song(id, amount_songs))

    temp_reconendation = set(np.concatenate(recodantions_per_song).tolist())
    return list(temp_reconendation)


def recomendation_system(ids: list[str], amount_songs: int = 10):
    if (len(ids) > 1):
        return recomend_multiple_songs(ids, amount_songs)
    return recomend_a_song(ids[0], amount_songs)


def filter_by_day(current_date: datetime, data: list[any]) -> list[any]:
    result = []
    for row in data:
        if (row['date'].weekday() == current_date.weekday()):
            result.append(row)

    return result

def is_morning(date_filter: datetime) -> bool:
    return date_filter.hour >= 5 and date_filter.hour <= 12

def is_afternoon(date_filter: datetime) -> bool:
    return date_filter.hour >= 13 and date_filter.hour <= 20

def is_nigth(date_filter: datetime) -> bool:
    return date_filter.hour >= 21 or date_filter.hour < 5

def filter_by_hours(current_date: datetime.datetime, data: list[any]) -> list[any]:
    result = []

    for row in data:
        if (is_morning(current_date) and is_morning(row['date'])):
            result.append(row)
            continue
        elif (is_afternoon(current_date) and is_afternoon(row['date'])):
            result.append(row)
            continue
        elif (is_nigth(current_date) and is_nigth(row['date'])):
            result.append(row)
            continue
    return result


def create_custom_playlist(id, amount_sogns: int = 10):
    res = DB.get_playbacks(db_instace=db_instance, user_id=id)
    date_now = datetime.datetime(2023, 4, 21, 14, 0, 0) # debug date
    # date_now = datetime.datetime.now()
    day_filter = filter_by_day(date_now, res)
    hour_filter = filter_by_hours(date_now, day_filter)

    return hour_filter


# EndPoints
@app.before_request
def start_timer():
    g.start = time.time()


@app.after_request
def log_request(response):
    if request.path == '/favicon.ico':
        return response
    elif request.path.startswith('/static'):
        return response

    now = time.time()
    duration = round(now - g.start, 2)
    dt = datetime.datetime.fromtimestamp(now)
    timestamp = rfc3339(dt, utc=True)

    ip = request.headers.get('X-Forwarded-For', request.remote_addr)
    host = request.host.split(':', 1)[0]
    args = dict(request.args)

    log_params = [
        ('method', request.method, 'blue'),
        ('path', request.path, 'blue'),
        ('status', response.status_code, 'yellow'),
        ('duration', duration, 'green'),
        ('time', timestamp, 'magenta'),
        ('ip', ip, 'red'),
        ('host', host, 'red'),
        ('params', args, 'blue')
    ]

    request_id = request.headers.get('X-Request-ID')
    if request_id:
        log_params.append(('request_id', request_id, 'yellow'))

    parts = []
    for name, value, color in log_params:
        part = colors.color("{}={}".format(name, value), fg=color)
        parts.append(part)
    line = " ".join(parts)

    app.logger.info(line)

    return response


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


@app.route('/api/create_playlist', methods=["GET"])
def create_playlist():
    song_ids_query = request.args.get('id', type=str)
    if (song_ids_query == ""):
        return make_response({'response': Bad_Response("Bad Request").Get_Response()}, 400)

    songs_ids = str(song_ids_query).split(',')
    songs = request.args.get('song_count', type=int)

    results = recomendation_system(songs_ids, songs)
    dto = Good_Response(results, len(results))
    return jsonify({'response': dto.Get_Response()}), 200


@app.route('/api/my_playlist', methods=["GET"])
def my_playlist():
    user_id = request.args.get('id', type=str)
    user_id = str(user_id).split(',')
    if (len(user_id) > 1):
        return make_response({'response': Bad_Response("Bad Request").Get_Response()}, 400)

    service_data = create_custom_playlist(user_id[0])
    dto = Good_Response(service_data, len(service_data))
    return jsonify({'response': dto.Get_Response()})
