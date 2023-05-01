import os
from typing import Any
import mysql.connector as mysql
from dotenv import load_dotenv
from datetime import datetime


load_dotenv()


def create_instance():
    return mysql.connect(
        user=os.getenv("DB_USER", "recc"),
        password=os.getenv("DB_PASSWORD", ""),
        host=os.getenv("DB_HOST", "127.0.0.1"),
        port=os.getenv("DB_PORT", "3306"),
        database=os.getenv("DB_DATABASE", "recc")
    )


def add_artist(db_instance, artist: dict):
    """Adds an Last.fm fetched artist into recc database"""
    try:
        query = (
            "INSERT INTO artists(name, mbid, listeners, image_url, last_fm_url) VALUES(%s, %s, %s, %s, %s)")
        values = (artist['name'], artist['mbid'], artist['listeners'],
                  artist['image'][-1]['#text'], artist['url'])
        print(f"Query: {query}\nValues: {values}")
        cursor = db_instance.cursor()
        cursor.execute(query, values)
        db_instance.commit()
        cursor.close()
    except Exception as err:
        print(f"Error adding artist to DB: {err}")


def add_track(db_instance, track: dict) -> str:
    """Adds an Last.fm fetched track into recc database"""
    try:
        cursor = db_instance.cursor()
        cursor.execute(f"SELECT * FROM tracks WHERE url='{track['url']}'")
        res = cursor.fetchall()
        print("res: ", res)
        if len(res) > 0:
            print("exists")
            return "exist"
        cursor.execute(
            f"INSERT INTO tracks(title, artist, duration, album, album_art_url, url) VALUES('{track['name']}', '{track['artist']['name']}', {track['duration']}, '{track['album']['title']}', '{track['album']['url']}', '{track['url']}')")
        db_instance.commit()
        cursor.close()
        return "added"
    except Exception as err:
        print(f"Error adding track to DB: {err}")
        return "error"


def get_song_id(db_instance, url: str) -> int:
    """Looks into the DB for a certain song and gets the Id"""
    try:
        cursor = db_instance.cursor()
        cursor.execute(f"SELECT id FROM tracks WHERE url='{url}'")
        res = cursor.fetchall()
        db_instance.commit()
        cursor.close()
        return res[0][0]
    except Exception as err:
        print(f"Error on this url: {url}")
        return -1


def get_user_playbacks(db_instance, user_id):
    try:
        cursor = db_instance.cursor()
        cursor.execute(f'SELECT user_id, track_id, created_at  FROM playbacks WHERE user_id={user_id}')
        data =  cursor.fetchall()
        db_instance.commit()
        if (len(data) < 1):
            return []
        results = []
        for row in data:
            temp_obj = {
                'user_id': row[0],
                'track_id': row[1],
                'date': row[2]
            }
            results.append(temp_obj)
        cursor.close()
        return results
    except Exception as err:
        print(f"Error on this user: {user_id}")
        return []


def get_playbacks(db_instance):
    try:
        cursor = db_instance.cursor()
        cursor.execute(f'SELECT user_id, track_id, created_at  FROM playbacks;')
        data =  cursor.fetchall()
        db_instance.commit()
        if (len(data) < 1):
            return []
        results = []
        for row in data:
            temp_obj = {
                'user_id': row[0],
                'track_id': row[1],
                'date': row[2]
            }
            results.append(temp_obj)
        cursor.close()
        return results
    except Exception as err:
        return []


def get_user_favorites(db_instance, user_id):
    try:
        cursor = db_instance.cursor()
        cursor.execute(f'SELECT track_id from preferred_tracks where user_id={user_id}')
        data = cursor.fetchall()
        db_instance.commit()
        if (len(data) <= 0): return []
        results = []
        for row in data:
            results.append(row[0])
        cursor.close()
        return results
    except Exception as err:
        return []


def get_playlist_by_title(db_instance, title: str, user_id: int):
    try:
        cursor = db_instance.cursor()
        cursor.execute(f'SELECT * FROM playlists WHERE user_id={user_id} AND title=\"{title}\"')
        data = cursor.fetchall()
        db_instance.commit()
        if (len(data) <= 0): return []
        results = []
        for row in data:
            results.append(row)
        cursor.close()
        return results
    except Exception as err:
        return []


def create_auto_playlist(db_instance, user_id: int, title: str, origin: str = "AUTO") -> int:
    """Creates a new auto playlists (using playbacks) and returns its id"""
    try:
        cursor = db_instance.cursor()
        query = ("INSERT INTO playlists (title, origin, user_id, created_at, updated_at) VALUES(%s, %s, %s, %s, %s)")
        time = datetime.now()
        values = (title, origin, user_id, time, time)
        cursor.execute(query, values)

        cursor.execute(f"SELECT id FROM playlists WHERE title=\"{title}\" AND origin=\"{origin}\" AND user_id=\"{user_id}\"")
        data = cursor.fetchall()
        db_instance.commit()
        id = data[-1][0]
        cursor.close()
        return id
    except Exception as err:
        print(f"Error adding tracks to playlist: {err}")


def add_tracks_to_playlist(db_instance, playlist_id: int, track_ids: list[int]) -> None:
    try:
        cursor = db_instance.cursor()
        for track_id in track_ids:
            query = ("INSERT INTO playlist_track (playlist_id, track_id) VALUES(%s, %s)")
            values = (playlist_id, track_id)
            cursor.execute(query, values)
        db_instance.commit()
        cursor.close()
    except Exception as err:
        print(f"Error adding tracks to playlist: {err}")