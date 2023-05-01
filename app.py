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
import pandas as pnd

db_instance = DB.create_instance()
learning_matrix = pd.read_csv('./recomendation_IA.csv', index_col=0)
valid_songs = learning_matrix.index.to_list()
app = Flask(__name__)
logging.basicConfig(level=logging.DEBUG)
print('App running...')

default_song_count = 8
MORNING = "Morning"
AFTERNOON = "Afternoon"
NIGHT = "Night"
FIRST_PLAYLIST_TITLE = "First playlist"

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
        recodantions_per_song.append(recomend_a_song(str(id), amount_songs))

    temp_reconendation = set(np.concatenate(recodantions_per_song).tolist())
    print(temp_reconendation)
    return list(temp_reconendation)


def recomendation_system(ids: list[str], amount_songs: int = 10):
    if (len(ids) <= 0):
        return []
    if (len(ids) > 1):
        return recomend_multiple_songs(ids, amount_songs)
    return recomend_a_song(str(ids[0]), amount_songs)


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


def same_month(current_date: datetime.datetime, date_check: datetime.datetime):
    return current_date.month == date_check.month


def get_playback_time(date: datetime.datetime) -> list[any]:
    if (is_morning(date)):
        return MORNING
    elif (is_afternoon(date)):
        return AFTERNOON
    elif (is_nigth(date)):
        return NIGHT


def filter_by_hours(current_date: datetime.datetime, data: list[any]) -> list[any]:
    result = []
    for row in data:
        if (is_morning(current_date) and is_morning(row['date'])):
            result.append(row)
        elif (is_afternoon(current_date) and is_afternoon(row['date'])):
            result.append(row)
        elif (is_nigth(current_date) and is_nigth(row['date'])):
            result.append(row)
    return result


def filter_by_month(current_date: datetime.datetime, data: list[any]) -> list[any]:
    result = []
    for row in data:
        if (same_month(current_date, row['date'])):
            result.append(row)
    return result


def get_playback_grouped(data: list[any]) -> list[any]:
    if (len(data) <= 0):
        return []
    df = pnd.DataFrame(data=data)
    df.drop(labels=['date', 'user_id'], inplace=True, axis=1)
    frecuency_ids = df['track_id'].value_counts().to_frame('count').rename_axis('track_id').reset_index()
    result = frecuency_ids.sort_values(by='count', ascending=False)['track_id'].to_list()
    return result


def check_frecuency_get_ids(data: list[any]) -> list[int]:
    if (len(data) <= 0):
        return []
    if (len(data) > 5):
        return data[:4]
    return data

def create_custom_playlist_favorites(id, amount_songs: int = 10):
    user_favorites = DB.get_user_favorites(db_instance, id)
    if (len(user_favorites) <= 0):
        return []
    ids_str = [str(id) for id in user_favorites]
    new_playlist = recomendation_system(ids_str, amount_songs)
    return new_playlist


def create_custom_playlist(id, amount_songs: int = 10) -> list[int] | str:
    """Creates a new playlist automatically generated either based on playbacks or preferred tracks"""
    # date_now = datetime.datetime(2023, 3, 10, 18, 0, 0) # debug date
    date_now = datetime.datetime.now()
    playback_time = get_playback_time(date_now)
    playlist_title = date_now.date().strftime("%d/%m/%Y") + " " + playback_time
    playbacks = DB.get_user_playbacks(db_instance=db_instance, user_id=id)
    origin = ""
    print(playbacks)
    if len(playbacks) > 0:
        origin = "AUTO"
        day_filter = filter_by_day(date_now, playbacks)
        hour_filter = filter_by_hours(date_now, day_filter)
        frecuency_data = get_playback_grouped(hour_filter)
        song_to_recomend = check_frecuency_get_ids(frecuency_data)
        result = recomendation_system(song_to_recomend, amount_songs)
    else:
        origin = "COLD"
        playlist_title = FIRST_PLAYLIST_TITLE
        result = create_custom_playlist_favorites(id, amount_songs)

    if len(DB.get_playlist_by_title(db_instance, playlist_title, id)) > 0:
        return [-1], "", playlist_title
    return result, origin, playlist_title


def all_time_playlist(amount_songs: int = 10):
    all_playbacks = DB.get_playbacks(db_instace=db_instance)
    top_songs = get_playback_grouped(all_playbacks)
    max_length = len(top_songs)

    return top_songs[:amount_songs] if (amount_songs <= max_length) else top_songs


def monthly_playlist(amount_song: int = 10):
    all_playbacks = DB.get_playbacks(db_instace=db_instance)
    date_now = datetime.datetime(2023, 3, 21, 14, 0, 0) # debug date
    # date_now = datetime.datetime.now()
    data_month = filter_by_month(date_now, all_playbacks)
    month_top = get_playback_grouped(data_month)
    max_length = len(month_top)

    return month_top[:amount_song] if (amount_song <= max_length) else month_top


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
        # TODO: enable colors for logger
        # part = colors.color("{}={}".format(name, value), fg=color)
        part = "{}={}".format(name, value)
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


@app.route('/api/create_playlist', methods=["POST"])
def create_playlist():
    """New playlist generated manually by the user (selecting tracks by its own)"""
    data = request.get_json()
    song_ids = data['song_ids']
    song_ids = [ str(i) for i in song_ids]
    playlist_id = data['playlist_id']
    if (song_ids == None):
        return make_response({'response': Bad_Response("Missing song_ids argument").Get_Response()}, 400)
    if (playlist_id == None):
        return make_response({'response': Bad_Response("Missing playlist_id argument").Get_Response()}, 400)
    song_count = request.form.get('song_count', type=int)
    if (song_count == None):
        song_count = default_song_count

    results = recomendation_system(song_ids, song_count)
    if playlist_id != None:
        DB.add_tracks_to_playlist(db_instance, playlist_id, results)
    dto = Good_Response(results, len(results))
    return jsonify({'response': dto.Get_Response()}, 200)


@app.route('/api/my_playlist', methods=["GET"])
def my_playlist():
    """Enpoint for cold start and playback based playlists"""
    user_id = request.args.get('user_id', type=int)
    if (user_id == None):
        return make_response({'response': Bad_Response("Missing argument \"user_id\"").Get_Response()}, 400)

    service_data, origin, title = create_custom_playlist(user_id)
    if (len(service_data) <= 0):
        return make_response({'response': Bad_Response("There are not enough playbacks").Get_Response()}, 400)
    elif service_data[0] == -1:
        return make_response({'response': Bad_Response(f"{title} already exists").Get_Response()}, 400)
    playlist_id = DB.create_auto_playlist(db_instance,  user_id, title, origin)
    DB.add_tracks_to_playlist(db_instance, playlist_id, service_data)

    dto = Good_Response(service_data, len(service_data))
    return jsonify({'response': dto.Get_Response()})


@app.route('/api/all_time_top', methods=["GET"])
def all_time_top():
    songs = request.args.get('song_count', type=int, default=0)
    results = None
    if (songs <= 0):
        results = all_time_playlist()
        dto = Good_Response(results, len(results))
        return jsonify({'response': dto.Get_Response()})
    results = all_time_playlist(songs)
    dto = Good_Response(results, len(results))
    return jsonify({'response': dto.Get_Response()})


@app.route('/api/monthtly_top', methods=["GET"])
def monthtly_top():
    songs = request.args.get('song_count', type=int, default=0)
    results = None
    if (songs <= 0):
        results = monthly_playlist()
        dto = Good_Response(results, len(results))
        return jsonify({'response': dto.Get_Response()})
    results = monthly_playlist(songs)
    dto = Good_Response(results, len(results))
    return jsonify({'response': dto.Get_Response()})
