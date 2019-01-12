<?php
//start session
session_start();

// Include config file and twitter PHP Library by Abraham Williams (abraham@abrah.am)
include_once("config.php");
include_once("includes/twitteroauth.php");
include_once("includes/functions.php");
$conn = dbcon();
?><?php
	if(isset($_SESSION['status']) && $_SESSION['status'] == 'verified') 
	{
		//Retrive variables
		$screen_name 		= $_SESSION['request_vars']['screen_name'];
		$twitter_id			= $_SESSION['request_vars']['user_id'];
		$oauth_token 		= $_SESSION['request_vars']['oauth_token'];
		$oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];
	
		//Show welcome message
//		echo '<div class="welcome_txt">Welcome <strong>'.$screen_name.'</strong> <a href="logout.php?logout">Logout</a>!</div>';
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
		
		//If user wants to tweet using form.
		if(isset($_POST["updateme"])) 
		{
			//Post text to twitter
		//	$my_update = $connection->post('statuses/update', array('status' => $_POST["updateme"]));
		//	die('<script type="text/javascript">window.top.location="index.php"</script>'); //redirect back to index.php
		}
		
			$quser_id = "select id from users where oauth_uid = {$twitter_id} limit 1";
			$result_id = mysqli_query($conn,$quser_id) or die(mysqli_error($conn));
			$user_id = mysqli_result($result_id, 0, 'id');		
		
		if(isset($_POST["submittask"])) 
		{

			
			//print_r($_POST);
			$feedname = mysqli_escape_string($conn,$_POST['feedname']);
			$url = mysqli_escape_string($conn,$_POST['rssurl']);
			$page_from = (int)($_POST['pagefrom']);	
			$page_to = (int)($_POST['pageto']);		
			$tweettextfilters = mysqli_escape_string($conn,$_POST['tweettextfilters']);	
			$postsignorewords = mysqli_escape_string($conn,$_POST['postsignorewords']);
			$postinterval = (int)($_POST['postinterval']);	
			$postoffset = (int)($_POST['postoffset']);			
			$postsequence = (int)($_POST['postsequence']);			
			$postrotation = (int)($_POST['postrotation']);	
			$tweetperpost = (int)($_POST['tweetsperpost']);	
			
// print_r($_POST); exit;
		

			$searchimage = (isset($_POST['searchimage']))?'y':'n'; //echo $searchimage;
			$onlyifimage = (isset($_POST['onlyifimage']))?'y':'n';	//echo $onlyifimage;
			$tweettext = mysqli_escape_string($conn,$_POST['tweettext']);
			$tweeturl = mysqli_escape_string($conn,$_POST['tweeturl']);				
			$tweeturlprefix = mysqli_escape_string($conn,$_POST['tweeturlprefix']);
			$tweeturlpostfix = mysqli_escape_string($conn,$_POST['tweeturlpostfix']);
			$tweeturlfilters = mysqli_escape_string($conn,$_POST['tweeturlfilters']);			
   			$tweetimage = mysqli_escape_string($conn,$_POST['tweetimage']);	
   			$shortenbit = mysqli_escape_string($conn,$_POST['shortenbit']);
//	print_r($_POST); exit;		   					
			$q = "insert into feeds (user_id, name, url, page_from, page_to, tweet_text_filters, tweet_url_filters,posts_ignore_words, post_interval, post_offset, post_sequence, post_rotation,	tweet_per_post, tweet_text,tweet_url, tweet_url_prefix,tweet_url_post_fix, tweet_image,search_image,
			only_if_image, shorten_bit) values ({$user_id},'{$feedname}', '{$url}',{$page_from},{$page_to},'{$tweettextfilters}','{$tweeturlfilters}','{$postsignorewords}',{$postinterval},{$postoffset},{$postsequence},{$postrotation},{$tweetperpost},'{$tweettext}','{$tweeturl}','{$tweeturlprefix}','{$tweeturlpostfix}','{$tweetimage}','{$searchimage}', '{$onlyifimage}','{$shortenbit}')"; //exit;
			$s = mysqli_query($conn,$q) or die(mysqli_error($conn));
		}		
		
		//show tweet form
	/*	echo '<div class="tweet_box">';
		echo '<form method="post" action="index.php"><table width="200" border="0" cellpadding="3">';
		echo '<tr>';
		echo '<td><textarea name="updateme" cols="60" rows="4"></textarea></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><input type="submit" value="Tweet" /></td>';
		echo '</tr></table></form>';
		echo '</div>';*/
		
?><!DOCTYPE html>
<html lang="en-us">
	<head>
		<title>Satyam Technologies - Drip Feed</title>
		<link rel="stylesheet" type="text/css" href="adminstyles.css" />
		<link type="shortcut icon" rel="icon" href="images/favicon_image.png"/>	
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="js/drip.js"></script>		
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="logo"><a href="http://sixthlife.net/tweeps/"><img src="../bootstrap/img/tweeps-curly.png" /></a></div>


				<div id="login_detail">
					<div class="login_logout"><a href="logout.php?logout"><img src="images/logout.png" /></a></div>
					<div class="login_username">Welcome <?php echo $screen_name; ?></div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
			<div id="inner_content">
				<div class="top_menus">
					<ul class="admin_menu">
						<li class="right_bordered"><a href="index.php" class="selected">Add Feed</a></li>
						<li class="right_bordered"><a href="tasks.php">Feeds</a></li>
						<li class="right_bordered"><a href="htasks.php">Histroy</a></li>		
						<li class="right_bordered"><a href="account.php">Account</a></li>
						<div class="clear"></div>
					</ul>
				</div>



	<div class="page_heading">Add New Feed </div>
<!--
				<div class="notification"><span>Info :</span> Category added Successfully.</div>
				<div class="notification_green"><span>Error :</span> Category added Successfully.</div>
-->
<br /><br />
				<div class="adding_product">	
	
	<form method="post" id="addfeedform" action="index.php"><table  border="0" cellpadding="3" width="100%">
	

				
				
<tr><td>Feed Name <img src="images/icon-help.png" width="15" height="15" id="feedname" /></td>
<td><input type="text" name="feedname" class="add_product_txtfld" id="tfeedname"  size="50" value=""/><input type="hidden" name="uid" class="uiduser" id="uiduser" value="<?php echo $user_id; ?>" />
<p class="message" id="pmfeedname" ></p>
<p class="helptext" id="pfeedname" >A Name for the feed for your reference only.</p>
  </td>
</tr>

<tr><td>RSS URL <img src="images/icon-help.png" width="15" height="15" id="rssurl" /></td>
<td><input type="text" name="rssurl" class="add_product_txtfld" size="50" value="" id="trssurl"/> 
<p class="message" id="pmrssurl" ></p>
<p class="helptext" id="prssurl" >RSS Feed URL of your blog Ex. http://sixthlife.net/feed/</p>
</td>
</tr>

<tr>
<td> Page (Optional) <img src="images/icon-help.png" width="15" height="15" id="pagenum" /></td>
<td>From: <input type="text" class="add_product_txtfld" style="width:50px;" name="pagefrom" id="pagefrom" size="5" value="1" />To: 
<input type="text" name="pageto" size="5" value="1" class="add_product_txtfld" id="pageto" style="width:50px;"/>
<p class="message" id="pmpagenum" ></p>
<p class="helptext" id="ppagenum" >For RSS feeds ranging over several pages Ex.  For <strong>RSS URL</strong> http://sixthlife.net/feed/?paged={page} Start and End page numbers can be set.  </p>
</td>
</tr>

<tr>
<td colspan="2"> <h2 style="color:#06b5f2;">Scheduling</h2></td>
</tr>

<tr>
<td>Check for New Posts</td>
<td><select name="postinterval" class="add_product_selectbox">
<option value="5">Every 5 minutes</option>
<option value="15">Every 15 minutes</option>
<option value="30">Every 30 minutes</option>
<option value="60">Every 1 hour</option>
<option value="360">Every 6 hours</option>
<option value="720">Every 12 hours</option>
<option value="1440">Every 24 hours</option>
</select></td>
</tr>

<!--
<tr>
<td>Offset</td>
<td><select name="postoffset" class="add_product_selectbox">
<option  value="0">No Offset</option>
<option value="1">Upto 1 minute</option>
<option  value="6">Upto 6 minutes</option>
<option  value="10">Upto 10 minutes</option>
</select></td>
</tr>
-->

<tr>
<td>Posting Sequence</td>
<td><select name="postsequence" class="add_product_selectbox">
<!--<option value="1">Oldest Item First</option>-->

<option value="2">Newest Item First</option>
<!--<option value="3">New Items Only</option>-->

</select></td>
</tr>


<tr>
<td>Rotation <img src="images/icon-help.png" width="15" height="15" id="postrotation" /></td>
<td><select name="postrotation" class="add_product_selectbox">
<option value="0">No Rotation</option>
<option  value="30">Rotate Posts</option>

</select>
<p class="helptext" id="ppostrotation" >You can resend posts from RSS Feeds more than once. This is especially useful if RSS Feed ranges over several pages and you have sufficient posts not to repeat yourself too often. </p>
</td>
</tr>
<!--
<tr>
<td>Tweets Per Posting</td>
<td><select name="tweetsperpost" class="add_product_selectbox">
<option>1</option>
<option>5</option>
</select></td>
</tr>
-->

<tr>
<td colspan="2"><h2 style="color:#06b5f2">Tweet Template</h2></td>
</tr>

<tr>
<td>Message</td>
<td><textarea name="tweettext" id="tweettext" class="add_product_textarea" cols="38" rows="6">{title} {category}</textarea>
<p class="message" id="pmtweettext" ></p>
</td>
</tr>
<tr>
<td>URL </td>
<td><input type="text" name="tweeturl" value="{link}" id="tweeturl" class="add_product_txtfld" size="50" />
<p class="message" id="pmtweeturl" ></p>
</td>
</tr>
<tr>
<td>Image Link  </td>
<td><!--
<input type="text" class="add_product_txtfld" name="tweetimage" id="tweetimage" value="" class="add_product_txtfld"  size="50"/><br />
--> <input type="checkbox" name="searchimage" id="searchimage" checked="checked" value="y" /> Post Images from content as available. 
<input type="checkbox" name="onlyifimage" id="onlyifimage" value="n" /> Tweet only if an Image is available.
</td>
</tr>
<tr>
<td colspan="2"><h2 style="color:#06b5f2">Advanced (Optional)</h2></td>
</tr>
<tr>
<td> Text Filters  <img src="images/icon-help.png" width="15" height="15" id="tweettextfilters" /></td>
<td><input type="text" name="tweettextfilters" id="ttweettextfilters" class="add_product_txtfld" size="50" />
<p class="helptext" id="ptweettextfilters" >Comma seperated list of words that you need to remove from tweets in case found. Ex. uncategorized,author,admin </p>
</td>
</tr>
<tr>
<td> Ignore Words <img src="images/icon-help.png" width="15" height="15" id="postsignorewords" /></td>
<td><input type="text" name="postsignorewords" id="tpostsignorewords" class="add_product_txtfld" size="50" />
<p class="helptext" id="ppostsignorewords" >Comma seperated list of words when present in a message will stop the tweet from being sent. Ex. discount,2014,2015 </p>
</td>
</tr>

</tr>
<tr>
<td> URL Filters <img src="images/icon-help.png" width="15" height="15" id="tweeturlfilters" /></td>
<td><input type="text" name="tweeturlfilters" size="50" class="add_product_txtfld" id="ttweeturlfilters" />
<p class="helptext" id="ptweeturlfilters" >When formatting the URL in certain way with URL Prefix or Postfix certain part of URl may need to be removed. Ex. http:// </p>
</td>
</tr>
<tr>
<td>URL Prefix <img src="images/icon-help.png" width="15" height="15" id="tweeturlprefix" /></td>
<td> <input type="text" name="tweeturlprefix" value="" size="50" class="add_product_txtfld" id="ttweeturlprefix" /> 
<p class="helptext" id="ptweeturlprefix" >When formatting the URL to be included in tweet the prefix to be added to each URL at the start. </p>
</td>
</tr>

<tr>
<td>URL Postfix <img src="images/icon-help.png" width="15" height="15" id="tweeturlpostfix" /></td>
<td><input type="text" class="add_product_txtfld" name="tweeturlpostfix" id="ttweeturlpostfix" value="" size="50" class="add_product_txtfld"/>
<p class="helptext" id="ptweeturlpostfix" >When formatting the URL to be included in tweet the postfix to be added to each URL at the end. </p>
</td>
</tr>




<tr>
<td>Shorten URL</td>
<td><select name="shortenbit" id="shortenbit" class="add_product_selectbox">
<option value="b">Bit.ly or Tiny URL </option>
<!--
<option value="t">Tiny URL</option>
-->
<option value="n">Do Not Shorten</option>
</select></td>
</tr>
<tr>
<td colspan="2"><div id="preview" style="width:485px;padding-top:10px; padding-bottom:10px; margin-top:10px; margin-bottom:10px; background: #FCFBE3; border: 1px solid #FCFFCE;"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" name="submittask" value="Add Feed" id="savefeed_button" class="add_product_btn" />
<input type="button" name="showpreview" id="showpreview" value="Preview" class="add_product_btn" /></td>
</tr>




</table>


</div>
</form></div><?php		
		
		//Get latest tweets
	//	$my_tweets = $connection->get('statuses/user_timeline', array('screen_name' => $screen_name, 'count' => 5));
		
	//	echo '<div class="tweet_list"><strong>Latest Tweets : </strong>';
	//	echo '<ul>';
	//	foreach ($my_tweets  as $my_tweet) {
	//		echo '<li>'.$my_tweet->text.' <br />-<i>'.$my_tweet->created_at.'</i></li>';
	//	}
	//	echo '</ul></div>';
?>  </div>
			</div>
	
		<div id="full_footer">
			<div id="inner_footer">
				<p>Created by Satyam Technologies &copy; 2016  </p>
			</div>
		</div>
	</body>
</html>
<?php			
	}else{
		//Display login button
	//	echo '<a href="process.php">Login</a>';
	
	header('location:../index.php');
	}
?>