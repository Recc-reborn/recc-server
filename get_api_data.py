from dotenv import load_dotenv
from requests import get
import os
import numpy as np
import pandas as pnd
import colorama
from colorama import just_fix_windows_console
just_fix_windows_console()


base_last_fm_endpoint = "http://ws.audioscrobbler.com/2.0"


def fetch_last_fm_tracks(tracks: list[dict]):
    """Fetches missing artists from Last.fm"""
    format = "json"
    method = "track.getInfo"
    last_fm_api_key = os.getenv("LAST_FM_API_KEY")
    # last_fm_api_key = "4ee5fa23159bc4e2e42356c2ac641895"
    # print(tracks)
    results = []
    for i in range(len(tracks)):
        try:
            url = (f"{base_last_fm_endpoint}?"
                   f"method={method}&"
                   f"format={format}&"
                   f"api_key={last_fm_api_key}&"
                   f"artist={tracks[i]['artist_name']}&"
                   f"track={tracks[i]['title']}")
            response = get(url, timeout=1)
            response_json = response.json()
            json_tracks = response_json["track"]
            results.append(
                {"song_id": tracks[i]['song_id'], "url": json_tracks['url']})
        except Exception as err:
            print(f"{colorama.Fore.RED}exception: {err}")
            colorama.Fore.RESET
            results.append({"song_id": tracks[i]['song_id'], "url": None})

    return results


if __name__ == "__main__":
    load_dotenv()
    data = pnd.read_csv(
        './temp_folder/tracks_to_add_db.csv').to_dict('records')
    results = fetch_last_fm_tracks(data)
    # print(results)
    dataframe = pnd.DataFrame.from_dict(results)
    dataframe.to_csv('./temp_folder/songs_urls.csv')
