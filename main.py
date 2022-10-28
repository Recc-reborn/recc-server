from http.server import HTTPServer

from request_handler.request_handler import ReccRequestHandler

if __name__ == "__main__":
    server = HTTPServer(('localhost', 9000), ReccRequestHandler)
    try:
        print("Starting server on localhost:9000")
        print("Press Ctrl+C to stop")
        server.serve_forever()
    except KeyboardInterrupt:
        pass
