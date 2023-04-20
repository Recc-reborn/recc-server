class New_Response(object):
    def __init__(self, response_data):
        self.data = response_data


class Good_Response(New_Response):
    def __init__(self, data, response_count):
        super().__init__(data)
        self.response_count = response_count

    def Get_Response(self):
        return {'data': self.data, 'dataCount': self.response_count}


class Bad_Response(New_Response):
    def __init__(self, data_Msg):
        super().__init__(data_Msg)

    def Get_Response(self):
        return {'data': self.data}
