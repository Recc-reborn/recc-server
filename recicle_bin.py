base_last_fm_endpoint = "http://ws.audioscrobbler.com/2.0"
db_instance = db.create_instance()

def read_artist_csv_as_numpy() -> list:
    missing_data = read_csv("csv/artists_to_add_db.csv").to_numpy()
    return np.transpose(missing_data)[0]

def read_tracks_csv_as_numpy():
    return read_csv("csv/tracks_to_add_db.csv").to_dict("records")

def fetch_last_fm_artists(artists: list):
    """Fetches missing artists from Last.fm"""
    format = "json"
    method = "artist.search"
    last_fm_api_key = os.getenv("LAST_FM_API_KEY")
    for artist in artists:
        try:
            url = (f"{base_last_fm_endpoint}?"
                   f"method={method}&"
                   f"format={format}&"
                   f"api_key={last_fm_api_key}&"
                   f"artist={artist}")
            response = get(url, timeout=3)
            response_json = response.json()
            artist = response_json["results"]["artistmatches"]["artist"][0]
            db.add_artist(db_instance, artist)
        except Exception as err:
            print(f"exception: {err}")

def fetch_last_fm_tracks(tracks: list[dict]):
    """Fetches missing artists from Last.fm"""
    format = "json"
    method = "track.getInfo"
    last_fm_api_key = os.getenv("LAST_FM_API_KEY")
    db_results = []
    for i in range(len(tracks)):
        try:
            url = (f"{base_last_fm_endpoint}?"
                   f"method={method}&"
                   f"format={format}&"
                   f"api_key={last_fm_api_key}&"
                   f"artist={tracks[i]['artist_name']}&"
                   f"track={tracks[i]['title']}")
            response = get(url, timeout=0.25)
            response_json = response.json()
            json_tracks = response_json["track"]
            res = db.add_track(db_instance, json_tracks)
            if res == "error":
                db_results.append(i)
        except Exception as err:
            print(f"exception: {err}")
    return db_results