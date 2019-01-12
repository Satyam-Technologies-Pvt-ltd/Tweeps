<?php
//start session
session_start();

// Include config file and twitter PHP Library by Abraham Williams (abraham@abrah.am)
include_once("config.php");
include_once("includes/twitteroauth.php");
include_once("includes/functions.php");
$conn =dbcon();
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
		$feedid = (int)$_GET['eid'];
		
		$query = "select * from feeds where id = {$feedid} limit 1";
		$res = mysqli_query($conn,$query) or die(mysqli_error($conn));
		
	//	print_r(mysqli_fetch_row($res));
		
		$oldfeedname = mysqli_result($res,0, 'name');
		$oldurl = mysqli_result($res,0, 'url');
		$oldpagefrom = mysqli_result($res,0, 'page_from');
		$oldpageto = mysqli_result($res,0, 'page_to');
		$oldtweettextfilters = mysqli_result($res,0, 'tweet_text_filters');
		$oldpostsignorewords = mysqli_result($res,0, 'posts_ignore_words');
		$oldpostinterval = mysqli_result($res,0, 'post_interval');
		$oldpostoffset = mysqli_result($res,0, 'post_offset');
		$oldpostsequence = mysqli_result($res,0, 'post_sequence');
		$oldpostrotation = mysqli_result($res,0, 'post_rotation');
		$oldtweetperpost = mysqli_result($res,0, 'tweet_per_post');
		$oldsearchimage = mysqli_result($res,0, 'search_image');
		$oldonlyifimage = mysqli_result($res,0, 'only_if_image');				
		$oldtweettext = mysqli_result($res,0, 'tweet_text');
		$oldtweeturl = mysqli_result($res,0, 'tweet_url');
		$oldtweeturlprefix = mysqli_result($res,0, 'tweet_url_prefix');
		$oldtweeturlpostfix = mysqli_result($res,0, 'tweet_url_post_fix');
		$oldtweetimage = mysqli_result($res,0, 'tweet_image');	
		$oldshortenbit = mysqli_result($res,0, 'shorten_bit');	
		
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
			
//print_r($_POST); exit;
		
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
			$q = "update feeds set user_id = {$user_id},name = '{$feedname}', url = '{$url}', page_from = {$page_from}, page_to = {$page_to}, tweet_text_filters=
			'{$tweettextfilters}', tweet_url_filters = '{$tweeturlfilters}', posts_ignore_words= '{$postsignorewords}', post_interval = {$postinterval}, post_offset={$postoffset}, post_sequence = {$postsequence},post_rotation={$postrotation},tweet_per_post =  {$tweetperpost},   tweet_text = '{$tweettext}',
			tweet_url = '{$tweeturl}', tweet_url_prefix = '{$tweeturlprefix}', tweet_url_post_fix = '{$tweeturlpostfix}', tweet_image = '{$tweetimage}', search_image='{$searchimage}', only_if_image= '{$onlyifimage}', shorten_bit = '{$shortenbit}' where id = {$feedid} limit 1";
			
			  
			$s = mysqli_query($conn,$q) or die(mysqli_error($conn));
		}		
		
		//show tweet form
	/*	echo '<div class="tweet_box">';
		echo '<form method="post" action="etasks.php?eid={}"><table width="200" border="0" cellpadding="3">';
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



	<div class="page_heading">Edit Feed </div>
<!--
				<div class="notification"><span>Info :</span> Category added Successfully.</div>
				<div class="notification_green"><span>Error :</span> Category added Successfully.</div>
-->
				<div class="adding_product">	
	
	<form method="post" id="addfeedform" action="etasks.php?eid=<?php if(isset($_GET['eid']) && $_GET['eid']!=''){ echo (int)$_GET['eid'];} else if(isset($_POST['feedid'])){ echo $_POST['feedid']; } ?>"><table  border="0" cellpadding="3" width="100%">
	

				
				
<tr><td>Feed Name <img src="images/icon-help.png" width="15" height="15" id="feedname" /></td>
<td><input type="text" name="feedname" class="add_product_txtfld" value="<?php if(isset($_POST['feedname'])){ echo $_POST['feedname'];} else{ echo $oldfeedname;}?>" id="tfeedname"  size="50" value=""/><input type="hidden" name="uid" class="uiduser" id="uiduser" value="<?php echo $user_id; ?>" />
<p class="message" id="pmfeedname" ></p>
<p class="helptext" id="pfeedname" >A Name for the feed for your reference only.</p>

<input type="hidden" name="feedid" value="<?php if(isset($_GET['eid']) && $_GET['eid']!=''){ echo (int)$_GET['eid'];} else if(isset($_POST['feedid'])){ echo $_POST['feedid']; } ?>" />
  </td>
</tr>

<tr><td>RSS URL <img src="images/icon-help.png" width="15" height="15" id="rssurl" /></td>
<td><input type="text" name="rssurl" class="add_product_txtfld" size="50" value="<?php if(isset($_POST['rssurl'])){ echo $_POST['rssurl'];} else{ echo $oldurl;}?>" id="trssurl"/> 
<p class="message" id="pmrssurl" ></p>
<p class="helptext" id="prssurl" >RSS Feed URL of your blog Ex. http://sixthlife.net/feed/</p>
</td>
</tr>

<tr>
<td> Page (Optional) <img src="images/icon-help.png" width="15" height="15" id="pagenum" /></td>
<td>From: <input type="text" class="add_product_txtfld" style="width:50px;" name="pagefrom" id="pagefrom" size="5" value="<?php if(isset($_POST['pagefrom'])){ echo $_POST['pagefrom'];} else{ echo $oldpagefrom;}?>" />To: 
<input type="text" name="pageto" size="5" value="<?php if(isset($_POST['pageto'])){ echo $_POST['pageto'];} else{ echo $oldpageto;}?>" class="add_product_txtfld" id="pageto" style="width:50px;"/>
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
<option value="5" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='5'){ echo 'selected="selected"';} else if($oldpostinterval=='5'){ echo 'selected="selected"';}?> >Every 5 minutes</option>
<option value="15" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='15'){ echo 'selected="selected"';} else if($oldpostinterval=='15'){ echo 'selected="selected"';}?> >Every 15 minutes</option>
<option value="30" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='30'){ echo 'selected="selected"';} else if($oldpostinterval=='30'){ echo 'selected="selected"';}?>>Every 30 minutes</option>
<option value="60" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='60'){ echo 'selected="selected"';} else if($oldpostinterval=='60'){ echo 'selected="selected"';}?>>Every 1 hour</option>
<option value="360" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='360'){ echo 'selected="selected"';} else if($oldpostinterval=='360'){ echo 'selected="selected"';}?>>Every 6 hours</option>
<option value="720" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='720'){ echo 'selected="selected"';} else if($oldpostinterval=='720'){ echo 'selected="selected"';}?>>Every 12 hours</option>
<option value="1440" <?php if(isset($_POST['postinterval']) &&  $_POST['postinterval']=='1440'){ echo 'selected="selected"';} else if($oldpostinterval=='1440'){ echo 'selected="selected"';}?>>Every 24 hours</option>
</select></td>
</tr>

<!--
<tr>
<td>Offset</td>
<td><select name="postoffset" class="add_product_selectbox">
<option  value="0" <?php if(isset($_POST['postoffset']) &&  $_POST['postoffset']=='0'){ echo 'selected="selected"';} else if($oldpostoffset=='0'){ echo 'selected="selected"';}?> >No Offset</option>
<option value="1" <?php if(isset($_POST['postoffset']) &&  $_POST['postoffset']=='1'){ echo 'selected="selected"';} else if($oldpostoffset=='1'){ echo 'selected="selected"';}?> >Upto 1 minute</option>
<option  value="6" <?php if(isset($_POST['postoffset']) &&  $_POST['postoffset']=='6'){ echo 'selected="selected"';} else if($oldpostoffset=='6'){ echo 'selected="selected"';}?> >Upto 6 minutes</option>
<option  value="10" <?php if(isset($_POST['postoffset']) &&  $_POST['postoffset']=='10'){ echo 'selected="selected"';} else if($oldpostoffset=='10'){ echo 'selected="selected"';}?> >Upto 10 minutes</option>
</select></td>
</tr>
-->

<tr>
<td>Posting Sequence</td>
<td><select name="postsequence" class="add_product_selectbox">
<!--<option value="1" <?php if(isset($_POST['postsequence']) &&  $_POST['postsequence']=='1'){ echo 'selected="selected"';} else if($oldpostoffset=='1'){ echo 'selected="selected"';}?> >Oldest Item First</option>-->

<option value="2" <?php if(isset($_POST['postsequence']) &&  $_POST['postsequence']=='2'){ echo 'selected="selected"';} else if($oldpostoffset=='2'){ echo 'selected="selected"';}?> >Newest Item First</option>
<!--<option value="3" <?php if(isset($_POST['postsequence']) &&  $_POST['postsequence']=='3'){ echo 'selected="selected"';} else if($oldpostoffset=='3'){ echo 'selected="selected"';}?> >New Items Only</option>-->

</select></td>
</tr>


<tr>
<td>Rotation <img src="images/icon-help.png" width="15" height="15" id="postrotation" /></td>
<td><select name="postrotation" class="add_product_selectbox">
<option value="0" <?php if(isset($_POST['postrotation']) &&  $_POST['postrotation']=='0'){ echo 'selected="selected"';} else if($oldpostrotation=='0'){ echo 'selected="selected"';}?> >No Rotation</option>
<option  value="30" <?php if(isset($_POST['postrotation']) &&  $_POST['postrotation']=='30'){ echo 'selected="selected"';} else if($oldpostrotation=='30'){ echo 'selected="selected"';}?> >Rotate Posts</option>

</select>
<p class="helptext" id="ppostrotation" >You can resend posts from RSS Feeds more than once. This is especially useful if RSS Feed ranges over several pages and you have sufficient posts not to repeat yourself too often. </p>
</td>
</tr>
<!--
<tr>
<td>Tweets Per Posting</td>
<td><select name="tweetsperpost" class="add_product_selectbox">
<option value="1" <?php if(isset($_POST['tweetsperpost']) &&  $_POST['tweetsperpost']=='1'){ echo 'selected="selected"';} else if($oldtweetsperpost=='1'){ echo 'selected="selected"';}?> >1</option>
<option value="5" <?php if(isset($_POST['tweetsperpost']) &&  $_POST['tweetsperpost']=='5'){ echo 'selected="selected"';} else if($oldtweetsperpost=='5'){ echo 'selected="selected"';}?> >5</option>
</select></td>
</tr>
-->

<tr>
<td colspan="2"><h2 style="color:#06b5f2">Tweet Template</h2></td>
</tr>

<tr>
<td>Message</td>
<td><textarea name="tweettext" id="tweettext" class="add_product_textarea" cols="38" rows="6"><?php if(isset($_POST['tweettext'])){ echo $_POST['tweettext'];} else{ echo $oldtweettext;}?></textarea>
<p class="message" id="pmtweettext" ></p>
</td>
</tr>
<tr>
<td>URL </td>
<td><input type="text" name="tweeturl" value="<?php if(isset($_POST['tweeturl']) ){ echo $_POST['tweeturl'];} else{ echo $oldtweeturl;}?>" id="tweeturl" class="add_product_txtfld" size="50" />
<p class="message" id="pmtweeturl" ></p>
</td>
</tr>
<tr>
<td>Image Link  </td>
<td><!--
<input type="text" class="add_product_txtfld" name="tweetimage" id="tweetimage" value="<?php if(isset($_POST['tweetimage'])){ echo $_POST['tweetimage'];} else{ echo $oldtweetimage;}?>" class="add_product_txtfld"  size="50"/><br />
--> <input type="checkbox" name="searchimage" id="searchimage" value="y" <?php if(isset($_POST['searchimage']) &&  $_POST['searchimage']=='y'){ echo 'checked="checked"';} else if($oldsearchimage=='y'){ echo 'checked="checked"';}?> /> Post Images from content as available. 
<input type="checkbox" name="onlyifimage" id="onlyifimage" value="y" <?php if(isset($_POST['onlyifimage']) &&  
$_POST['onlyifimage']=='y'){ echo 'checked="checked"';} else if($oldonlyifimage=='y'){ echo 'checked="checked"';}?> /> Tweet only if an Image is available.
</td>
</tr>
<tr>
<td colspan="2"><h2 style="color:#06b5f2">Advanced (Optional)</h2></td>
</tr>
<tr>
<td> Text Filters  <img src="images/icon-help.png" width="15" height="15" id="tweettextfilters" /></td>
<td><input type="text" name="tweettextfilters" class="add_product_txtfld" id="ttweettextfilters" size="50"  value="<?php if(isset($_POST['tweettextfilters'])){ echo $_POST['tweettextfilters'];} else{ echo $oldtweettextfilters;}?>" />
<p class="helptext" id="ptweettextfilters" >Comma seperated list of words that you need to remove from tweets in case found. Ex. uncategorized,author,admin </p>
</td>
</tr>
<tr>
<td> Ignore Words <img src="images/icon-help.png" width="15" height="15" id="postsignorewords" /></td>
<td><input type="text" name="postsignorewords" id="tpostsignorewords" class="add_product_txtfld" size="50" value="<?php if(isset($_POST['postsignorewords'])){ echo $_POST['postsignorewords'];} else{ echo $oldpostsignorewords;}?>" />
<p class="helptext" id="ppostsignorewords" >Comma seperated list of words when present in a message will stop the tweet from being sent. Ex. discount,2014,2015 </p>
</td>
</tr>

</tr>
<tr>
<td> URL Filters <img src="images/icon-help.png" width="15" height="15" id="tweeturlfilters" /></td>
<td><input type="text" name="tweeturlfilters" size="50" id="ttweeturlfilters" class="add_product_txtfld" value="<?php if(isset($_POST['tweeturlfilters'])){ echo $_POST['tweeturlfilters'];} else{ echo $oldtweeturlfilters;}?>"  />
<p class="helptext" id="ptweeturlfilters" >When formatting the URL in certain way with URL Prefix or Postfix certain part of URl may need to be removed. Ex. http:// </p>
</td>
</tr>
<tr>
<td>URL Prefix <img src="images/icon-help.png" width="15" height="15" id="tweeturlprefix" /></td>
<td> <input type="text" name="tweeturlprefix"  size="50" id="ttweeturlprefix" class="add_product_txtfld" 
value="<?php if(isset($_POST['tweeturlprefix'])){ echo $_POST['tweeturlprefix'];} else{ echo $oldtweeturlprefix;}?>"  /> 
<p class="helptext" id="ptweeturlprefix" >When formatting the URL to be included in tweet the prefix to be added to each URL at the start. </p>
</td>
</tr>

<tr>
<td>URL Postfix <img src="images/icon-help.png" width="15" height="15" id="tweeturlpostfix" /></td>
<td><input type="text" class="add_product_txtfld" name="ttweeturlpostfix" value="<?php if(isset($_POST['ttweeturlpostfix'])){ echo $_POST['ttweeturlpostfix'];} else{ echo $oldtweeturlpostfix;}?>" size="50" class="add_product_txtfld" id="ttweeturlpostfix" />
<p class="helptext" id="ptweeturlpostfix" >When formatting the URL to be included in tweet the postfix to be added to each URL at the end. </p>
</td>
</tr>




<tr>
<td>Shorten URL</td>
<td><select name="shortenbit" id="shortenbit" class="add_product_selectbox">
<option value="b" <?php if(isset($_POST['shortenbit']) &&  $_POST['shortenbit']=='b'){ echo 'selected="selected"';} else if(!isset($_POST['shortenbit']) && $oldshortenbit=='b'){ echo 'selected="selected"';}?> >Bit.ly or Tiny URL </option>
<!--
<option value="t">Tiny URL</option>
-->
<option value="n" <?php if(isset($_POST['shortenbit']) &&  $_POST['shortenbit']=='n'){ echo 'selected="selected"';} else if(!isset($_POST['shortenbit']) && $oldshortenbit=='n'){ echo 'selected="selected"';}?> >Do Not Shorten</option>
</select></td>
</tr>
<tr>
<td colspan="2"><div id="preview" style="width:485px;padding-top:10px; padding-bottom:10px; margin-top:10px; margin-bottom:10px; background: #FCFBE3; border: 1px solid #FCFFCE;"></div></td>
</tr>
<tr>
<td colspan="2"><input type="submit" name="submittask" value="Save Feed" id="savefeed_button" class="add_product_btn" />
<input type="button" name="showpreview" id="showpreview" value="Preview" class="add_product_btn" /></td>
</tr>




</table>



</form><?php		
		
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
	
	header('location: ../index.php');
	}
?>