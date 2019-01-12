Tweeps RSS to twitter scheduler 
Installation
1.	Extract the Files you purchased
2.	Upload tweeps folder on your server
3.	Create a database,  database user and password
4.	Import Database from localhost.sql file.
5.	You need to make few changes to the tweeps/dashboard/config.php file.
a.	Add database information
b.	Add base installation url For ex: http://yoursite.com/tweeps/
c.	Add Twitter Consumer Key and Secret. You can generate at https://apps.twitter.com/ 
d.	Add Twitter callback URL For Ex. http://yoursite.com/tweeps/dashboard/process.php 
6.	At this stage you should be able to login to script with twitter at http://yoursite.com/tweeps/ 
Configuration
URL shortening is done by bit.ly and tinyurl.com. If you want to use bit.ly please add bit.ly username and API Key in the “Accounts” tab and save details. 
For more information on generating bit.ly credentials please refer http://support.bitly.com/knowledgebase/articles/76785-how-do-i-find-my-api-key- 
Scheduling Tweets and How to’s
For information on adding RSS Feeds and troubleshooting please refer to http://satyamtechnologies.net/blog/ 
