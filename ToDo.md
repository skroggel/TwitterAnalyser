- Add unique key for account_id to account-table (problem with @lisapaus)
- Add unique key to tweet_id to tweet-table. Check:
```
SELECT tweet_id, type, user_name, full_text, deleted FROM `tweet` WHERE 
tweet_id IN (
	SELECT tweet_id FROM `tweet` WHERE 1 =1 GROUP BY tweet_id HAVING COUNT(tweet_id) > 1
) 
AND type = 'searchTo'
ORDER BY user_name, type
```

Delete duplicate tweets:
```
DELETE FROM tweet WHERE 
tweet_id IN (
	SELECT tweet_id FROM `tweetTemp` WHERE 1 =1 GROUP BY tweet_id HAVING COUNT(tweet_id) > 1
) 
AND type = 'searchTo'
```