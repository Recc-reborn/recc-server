from app import app
from waitress import serve
from dotenv import load_dotenv
import os

if __name__ == "__main__":
    load_dotenv()
    # tracks = read_tracks_csv_as_numpy()
    # id_error = fetch_last_fm_tracks(tracks)
    # pd.DataFrame(data=id_error, columns=["id errors"]).to_csv("csv/tracks_conflicts.csv", index=False)
    # fetch_last_fm_artists(artists, "")
    # print("finished...")
    serve(app, host="0.0.0.0", port=os.getenv("APP_PORT", 5000))
    # db_instance.close()