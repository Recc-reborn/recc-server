from app import app
from waitress import serve
from dotenv import load_dotenv
import os

if __name__ == "__main__":
    load_dotenv()
    serve(app, host="0.0.0.0", port=os.getenv("APP_PORT", 5000), _quiet=True)
