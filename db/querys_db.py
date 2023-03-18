from sqlalchemy import Engine, text


def query_all_tracks(connection_engine: Engine):
    with connection_engine.connect() as conn:
        result = conn.execute(text("select * from tracks limit 10"))
        return result
