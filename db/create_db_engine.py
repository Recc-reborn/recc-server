import os
from sqlalchemy import create_engine

def create_db_engine():
    db_user = os.getenv("DB_USER", "root")
    db_password = os.getenv("DB_PASSWORD", "")
    db_host = os.getenv("DB_HOST", "127.0.0.1")
    db_port = os.getenv("DB_PORT", "5432")
    db_database = os.getenv("DB_DATABASE", "recc")

    connection_string = "mariadb+pymysql://{}:{}@{}:{}/{}?charset=utf8mb4".format(
        db_user,
        db_password,
        db_host,
        db_port,
        db_database,
    )

    return create_engine(connection_string, future = True)
