- Add unique key for account_id to account-table (problem with @lisapaus). Check:
```
SELECT uid, user_id, create_timestamp, user_name, is_secondary, is_suggestion FROM account WHERE 
user_id IN (
    SELECT account_sub1.user_id FROM account AS account_sub1 WHERE account_sub1.user_id > 0 GROUP BY account_sub1.user_id HAVING COUNT(account_sub1.user_id) > 1
) 
ORDER BY user_id, user_name
```

- Add unique key to tweet_id to tweet-table. Check:
```
SELECT tweet_id, create_timestamp, type, user_name, full_text, deleted FROM tweet WHERE 
tweet_id IN (
	SELECT tweet_id FROM `tweet` WHERE 1 =1 GROUP BY tweet_id HAVING COUNT(tweet_id) > 1
) 
AND type = 'searchTo'
ORDER BY user_name, type
```
