import pandas as pnd

data = [
    {
        "date": "Fri, 10 Mar 2023 17:52:59 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:53:19 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:53:20 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:53:20 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:53:21 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:56:04 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:56:06 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:56:07 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:12 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:13 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:14 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:16 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:17 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:20 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:58:58 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:59:08 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:59:08 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 17:59:09 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:15 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:45 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:47 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:57 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:58 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:58 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:00:59 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:07 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:08 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:15 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:15 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:15 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:16 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:16 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:28 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:29 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:30 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:30 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:31 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:42 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:01:42 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:07:49 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:07:53 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:07:56 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:07:56 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:07:56 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:08:47 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:02 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:16 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:17 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:18 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:19 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:19 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:21 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:22 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:22 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:35 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:36 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:36 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:37 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:54 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:55 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:09:55 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:44 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:44 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:45 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:49 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:53 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:54 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:58 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:10:59 GMT",
        "track_id": 1,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:11:34 GMT",
        "track_id": 23,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:11:35 GMT",
        "track_id": 23,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:12:00 GMT",
        "track_id": 23,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:12:05 GMT",
        "track_id": 23,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:12:05 GMT",
        "track_id": 23,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:12:08 GMT",
        "track_id": 232,
        "user_id": 9
    },
    {
        "date": "Fri, 10 Mar 2023 18:12:21 GMT",
        "track_id": 232,
        "user_id": 9
    }
]

df = pnd.DataFrame(data=data)
df.drop(labels=['date', 'user_id'], inplace=True, axis=1)

grouped_df = df['track_id'].value_counts().to_frame('count').rename_axis('track_id').reset_index()

grouped_df.sort_values(by=['count'])

print(grouped_df)