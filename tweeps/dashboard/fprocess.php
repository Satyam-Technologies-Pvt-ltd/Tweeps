<?php

include_once("config.php");
include_once("includes/twitteroauth.php");
include_once("includes/functions.php");
include 'includes/bitly.php';
$conn = dbcon();

removeautopause24();



$CONSUMER_KEY=CONSUMER_KEY;
$CONSUMER_SECRET=CONSUMER_SECRET;

$qdata1 = "select id from feeds where TIMESTAMPDIFF(MINUTE,  `current_tweet`, NOW()) >= `post_interval` and status='Active' and url!=''";
$rdata1 = mysqli_query($conn,$qdata1) or die(mysqli_error($conn));
//echo '<br /><br />' .mysqli_num_rows($rdata1).'<br /><br />'; //exit;

$qdata = "select * from feeds where TIMESTAMPDIFF(MINUTE,  `current_tweet`, NOW()) >= `post_interval` and status='Active' and url!='' limit 5";

 //$qdata = "select  now(), current_tweet, TIMESTAMPDIFF(MINUTE,  `current_tweet`, NOW()) from feeds";

$rdata = mysqli_query($conn,$qdata) or die(mysqli_error($conn));
//echo mysqli_num_rows($rdata); //exit;
$feed = '';

$xml = '';

while($row = mysqli_fetch_assoc($rdata)){
//	print_r($row); continue;
	$qcheck = "select id from history where feed_id = {$row['id']} and (log_status = 'Error'|| log like '%account is suspended%' || log like '%To protect our users from spam%'|| log like '%authoriz%') and time > DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
	$qres = mysqli_query($conn,$qcheck) or die(mysqli_error($conn));
	//echo mysqli_num_rows($qres);
	if(mysqli_num_rows($qres)>10){
	$qust = "update feeds set status = 'Paused', autopaused = 'y' , current_tweet = now()  where id = ".$row['id'] ;
	$rust = mysqli_query($conn,$qust) or die(mysqli_error($conn));		
		continue;
	}
	else if($row['url']==''|| in_array($row['user_id'], array(28,30))){
		continue;
	}
	
	$qcheckuser = "select id from history where user_id = {$row['user_id']} and (log like '%account is suspended%' || log like '%To protect our users from spam%'|| log like '%authoriz%') and time > DATE_SUB(NOW(), INTERVAL 300 MINUTE)";
	$qresuser = mysqli_query($conn,$qcheckuser) or die(mysqli_error($conn));	
		if(mysqli_num_rows($qresuser)>5){
		$q2 = "update users set account_status = 'Paused' where id =".$row['user_id']." limit 1";
		$r2 = mysqli_query($conn,$q2) or die(mysqli_error($conn));
			}
	

$qus = "select account_status from users where id = ".$row['user_id']." and account_status!='Active'";
$reu = mysqli_query($conn,$qus) or die(mysqli_error($conn));

	if(mysqli_num_rows($reu)>0){
	continue;	
	}
	
//	print_r($row); exit;
	$linkkey = 'link';
	$categorykey = 'category'; 
	$titlekey = 'title';
		
	$page = ($row['page_to']>0)?(($row['current_page']>0)?$row['current_page']:$row['page_from']):0;
	
	$page = ($page>$row['page_to'])?$row['page_to']:$page;
	

	
	$tweet_count = $row['current_tweet_count'];
	
	
	$feedurl = str_replace('{page}',$page,$row['url']); 	//	echo $feedurl;
	if($feedurl==''){continue;}
	$feed = get_web_page($feedurl);

	$xml = simplexml_load_string($feed, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	$array = $array['channel']['item']; //print_r($array);
	if(empty($array) || count($array)<1){ $array = json_decode($json,TRUE); $array = $array['entry'];}	
	$newtweetarray = array(); 
	if( $tweet_count==NULL)	{
	$newtweetarray = $array[0];	
	$new_tweet_count = 0;
	$qu = 'update feeds set current_tweet_count = 0  where id = '.$row['id'];
	$ru = mysqli_query($conn,$qu) or die(mysqli_error($conn));	
	}
	else if(isset($array[$tweet_count+1]) ){
		$newtweetarray = $array[$tweet_count+1];
		$new_tweet_count = $tweet_count+1;
		$qu = 'update feeds set current_tweet_count = '.$new_tweet_count.'  where id = '.$row['id'];
		$ru = mysqli_query($conn,$qu) or die(mysqli_error($conn));
	}
	else if(!isset($array[$tweet_count+1]) && $page<$row['page_to']){
	$page = $page+1;
	$feedurl = str_replace('{page}',$page,$row['url']);
	
	$feed = get_web_page($feedurl);
	
	$xml = simplexml_load_string($feed, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	$array = $array['channel']['item'];	
	$newtweetarray = $array[$tweet_count+1];
	$qu = 'update feeds set current_tweet_count = null , current_page ='.$page.'  where id = '.$row['id'];
	$ru = mysqli_query($conn,$qu) or die(mysqli_error($conn));	
			
	}
	else if($row['post_rotation']!==0 && $page == $row['page_to'] && !isset($array[$tweet_count+1]) ){
	$qu = 'update feeds set current_tweet_count = null , current_page =0 where id = '.$row['id'];
	$ru = mysqli_query($conn,$qu) or die(mysqli_error($conn));
	
	$rpage = $row['page_from'];
	$feedurl = str_replace('{page}',$rpage,$row['url']);
	
	$feed = get_web_page($feedurl);

	$xml = simplexml_load_string($feed, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	$array = $array['channel']['item'];	
	$newtweetarray = $array[0];
	$qu1 = 'update feeds set current_tweet_count = 0 , current_page ='.$rpage.'  where id = '.$row['id'];
	$ru = mysqli_query($conn,$qu1) or die(mysqli_error($conn));	
	
	$newtweetarray = $array[0];		
	}
	
 //	print_r($newtweetarray) ; 
 
	if(strpos($newtweetarray['description'], 'src')!==FALSE){
$imgs = explode('src="',$newtweetarray['description']);
 $imgs = explode('"',$imgs[1]) ;
  $imgs = $imgs[0]	;
 $newtweetarray['imageurl'] =$imgs;
  }	
 
	$qusers = "select * from users where id = {$row['user_id']} limit 1";
	$resusers = mysqli_query($conn, $qusers) or die(mysqli_error($conn));
	$user = array();
	

	
	while($row1 = mysqli_fetch_array($resusers)){
//	print_r($row);
	$user[] = array('username'=> $row1['username'],'OAUTH_TOKEN'=>$row1['oauth_token'], 'OAUTH_SECRET'=>$row1['oauth_secret'], 'autofollow'=>$row1['autofollow'],'autofollowmessage'=>$row1['autofollowmessage'], 'bitlyusername'=> $row['bitlyusername'], 'bitlyapikey'=> $row['bitlyapikey']);
	}
	
	
		$chktex = mysqli_escape_string($conn,$newtweetarray[$titlekey]);

	 	$qcheck2 = "select title from history where title like '%{$chktex}%' and user_id = '{$row['user_id']}' and time > DATE_SUB(NOW(), INTERVAL 1440 MINUTE)";
 	
 	$rescheck2 = mysqli_query($conn,$qcheck2) or die(mysqli_query($conn,$qcheck2));
 	$duplicate =  mysqli_num_rows($rescheck2);//exit;
 	//echo '<br />'.$duplicate.'<br />'; //continue;
//$duplicate = 1;
	
$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $user[0]['OAUTH_TOKEN'], $user[0]['OAUTH_SECRET']); //print_r($user); exit;
if(is_array($newtweetarray[$titlekey])){
	$newtweetarray[$titlekey] = recursiveFind($newtweetarray[$titlekey], $titlekey);
	if(count($newtweetarray[$titlekey]>0))	{
		$newtweetarray[$titlekey] = $newtweetarray[$titlekey][0];
	}
}
	$newtitle = $newtweetarray[$titlekey];	
	if(strlen($newtweetarray[$titlekey])>60){
	
	$newtitle = str_split($newtweetarray[$titlekey], 60);
	$newtitle = $newtitle[0].'..';
	} //print_r($newtweetarray[$titlekey]);	


$status_message = str_ireplace("{".$titlekey."}", $newtitle, $row['tweet_text']) ; 

//echo $status_message; exit;
 
$categories = $newtweetarray[$categorykey];
$hash = '';

if(!is_array($categories)&& $categories!=''){
	$categories = str_replace(',','',$categories);
	$category = str_replace('/','',$categories);
	$categories = explode(' ',$categories);
	
//	$hash =' #'. $categories;
	
	
}
 if(is_array($categories)){
$i = 0;
	$categoriesm = recursiveFind($categories, 'term'); 
	
	if(count($categoriesm)==0){
	$categoriesm =	array_iunique($categories);
	}
$categoriesm = array_iunique($categoriesm);
foreach($categoriesm as $category){
	$category = str_replace(',','',$category);
	$category = str_replace('/','',$category);
	$category = explode(' ',$category);
	$category = $category[0];
	
	if(strtolower($category)!== 'uncategorized' && strtolower($category)!== 'your'&& strtolower($category)!== 'useful'&& 
	strtolower($category)!== 'nicheindustry'&& strtolower($category)!== 'about'&& strtolower($category)!== 'about'&& strtolower($category)!== 'all'&& strtolower($category)!== 'case'&& strtolower($category)!== 'getting'&& strtolower($category)!== 'resources' ){
	if($i<3 && strpos($hash,$category )===FALSE){

	$hash .= ' #'. $category;
	}
	
	$i++;
	}
}
}



 if(strlen($status_message)>70){
$hash = explode(' ',$hash);
$status_message = str_replace('{'.$categorykey.'}', $hash[0], $status_message) ;

} 
else{
$status_message = str_replace('{'.$categorykey.'}', $hash, $status_message) ;	
}

 if(strlen($status_message)>70){

$status_message = explode('#',$status_message);
$status_message = $status_message[0];
}

//echo $status_message;exit;
		$imageurl = '';

 $xmlFeed=@simplexml_load_file($feedurl);

if(  ($row['search_image']=='y'|| $row['only_if_image']=='y' )&& $imageurl=='' ){
	if(isset($xmlFeed->channel->item)){
foreach($xmlFeed->channel->item as $item){
	//echo $newtweetarray[$titlekey] .' '. $item->title;
	if($newtweetarray[$titlekey] == $item->title ){ //echo 'hi'; exit;
    $media = $item->children('http://search.yahoo.com/mrss/');
foreach($media as $image){

	     $aurl = $image->attributes()->url; //echo $aurl; exit;
	     $aurl = explode('?',$aurl);
 $aurl = $aurl[0];
 $aurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl);
	     		$size=@getimagesize($aurl);
		if( $imageurl=='' && get_web_page($aurl)!==FALSE ){
		  	if ((!isset($size[0]) || $size[0]>390 )&&(!isset($size[1]) || $size[1]>250)){	
		 $imageurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl) ;}
		}

   
	} 
	}
}
}
else if(isset($xmlFeed->entry)){
foreach($xmlFeed->entry as $item){
	//echo $newtweetarray[$titlekey] .' '. $item->title;
	if($newtweetarray[$titlekey] == $item->title ){ //echo 'hi'; exit;
    $media = $item->children('http://search.yahoo.com/mrss/');
foreach($media as $image){

	     $aurl = $image->attributes()->url; //echo $aurl; exit;
	     $aurl = explode('?',$aurl);
 $aurl = $aurl[0];
 $aurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl);
	     		$size=@getimagesize($aurl);
		if( $imageurl=='' && get_web_page($aurl)!==FALSE  ){
		  	if ((!isset($size[0]) || $size[0]>390 )&&(!isset($size[1]) || $size[1]>250)){	
		 $imageurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl) ;}
		}

   
	} 
	}
}	
	
}

	if(is_array($newtweetarray[$linkkey])){	
	$newtweetarray[$linkkey] =	recursiveFind($newtweetarray[$linkkey], 'href'); 
	if(is_array($newtweetarray[$linkkey])){
	$newtweetarray[$linkkey] = $newtweetarray[$linkkey][0];}
//	print_r($newtweetarray[$linkkey]);
}
	if(is_array($newtweetarray[$linkkey]) && isset($newtweetarray [$linkkey]['@attributes']['href'])){
		
		$newtweetarray[$linkkey] = $newtweetarray[$linkkey]['@attributes']['href'];
	}
//echo $newtweetarray[$linkkey]. ' wrw'; exit; 
 
 if($imageurl == '' &&  $newtweetarray[$linkkey]!=='' && ( $row['search_image']=='y' || $row['only_if_image']=='y')){
 //	if(strpos($newtweetarray[$linkkey], 'exeoent')!==false){
// print_r($newtweetarray);
 //}
libxml_use_internal_errors(true);
$doc = new DomDocument();

 $webpage = get_web_page($newtweetarray[$linkkey]); //exit;
if($webpage!=''){
$doc->loadHTML($webpage);
$xpath = new DOMXPath($doc);
$query = '//*/meta[starts-with(@property, \'og:\')]';
$queryT = '';
$metas = $xpath->query($query);
foreach ($metas as $meta) {
    $property = $meta->getAttribute('property');
    $content = $meta->getAttribute('content');
    if($property=='og:image'){
    	
    		$size=@getimagesize($content);
	if( get_web_page($content)!==FALSE ){
	 	 	if ((!isset($size[0]) || $size[0]>390 )&&(!isset($size[1]) || $size[1]>250)){	
 	$imageurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$content) ;}
	}   	
    }
}	
 $doc->preserveWhiteSpace = false;
$images = $doc->getElementsByTagName('img');
foreach ($images as $image) {
  $aurl = $image->getAttribute('src');
$aurl = explode('?',$aurl);
 $aurl = $aurl[0];
 $aurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl);
  		$size=@getimagesize($aurl);
	if( get_web_page($aurl)!==FALSE ){
	 	 	if ((!isset($size[0]) || $size[0]>390 )&&(!isset($size[1]) || $size[1]>250)){	
 	$imageurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl) ;}
	}  
  
}	
}
}
 $imageurl = explode('?',$imageurl);
 $imageurl = $imageurl[0];
 
 }
 
	if(isset($newtweetarray['imageurl']) && $imageurl==''){
			$imageurl = $newtweetarray['imageurl'];
	}
 
 $containsignorewords = false;
 
 if($row['posts_ignore_words']!==''){
$ignorew = explode(',', $row['posts_ignore_words'])  ;

foreach($ignorew as $w){
	if(strpos($status_message, trim($w))!==false){
	$containsignorewords = true;
	}
}
}

if($row['tweet_text_filters']!==''){
	$filters = explode(',',$row['tweet_text_filters']);
	foreach($filters as $filter){
		$status_message = str_replace($filter, '', $status_message);
	}
}

	$status_message = str_replace('# #', ' #', $status_message);	
	$status_message = str_replace('# #', ' #', $status_message);
	$status_message = str_replace('#  #', ' #', $status_message);
	$status_message = str_replace(' # ', ' ', $status_message); 
	$status_message = str_replace('# ', '', $status_message);
	$replaced = preg_replace('/[^\x00-\x7F]+/', '', $status_message);
//print_r($newtweetarray) ;
//print_r($user); exit;
//echo print_r($row);
if($imageurl!=''){
	$qcheckn = "select id from history where feed_id = {$row['id']} and user_id = {$row['user_id']} 
	and image_url='{$imageurl}'  and time > DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
	$qresn = mysqli_query($conn,$qcheckn) or die(mysqli_error($conn));
	//echo mysqli_num_rows($qres);
	if(mysqli_num_rows($qresn)>5){
	$qustn = "update feeds set status = 'Paused'  where id = ".$row['id'] ;
	$rustn = mysqli_query($conn,$qustn) or die(mysqli_error($conn));		
		continue;
	}
}	
	
//echo	$chktex = mysqli_escape_string($conn,$newtweetarray[$titlekey]);
//$aimg = explode('/', $imageurl);
//$aimg = $aimg[count($aimg)-1];
$aimg = mysqli_escape_string($conn,$imageurl);
if($aimg!='' and $duplicate==0){
	 	$qcheck3 = "select image_url from history where image_url like '%{$aimg}%' and user_id = '{$row['user_id']}' and time > DATE_SUB(NOW(), INTERVAL 1440 MINUTE)";
 
 //	echo 	$qcheck3 = "select image_url from history where image_url like '%{$aimg}%' and user_id = '{$row['user_id']}'";	
 	$rescheck3 = mysqli_query($conn,$qcheck3) or die(mysqli_query($conn,$qcheck3));
 	$duplicate =  mysqli_num_rows($rescheck3);//exit;
 //	echo $aimg.'<br />';
 //	echo mysqli_result($rescheck3, 0 , 'image_url').'<br /><br /><br />';
 //	echo '<br />'.$duplicate.'<br /><br /><br />'; //continue;	
}	
	


if($imageurl!==''  && $containsignorewords==false && get_web_page($imageurl) && strlen($status_message)>20 && $duplicate==0){
$content = $connection->get('account/verify_credentials');
		if(strpos($row['tweet_url_postfix'], 'http://')!==FALSE || strpos($row['tweet_url_postfix'], 'www')!==FALSE ){
		$row['tweet_url_postfix'] = urlencode($row['tweet_url_postfix']);
	}
if($row['tweet_url_filters']!==''){
$newlink =($row['tweet_url_prefix']) . urlencode( str_replace($row['tweet_url_filters'],'',$newtweetarray[$linkkey])) .($row['tweet_url_postfix']);
}

else{
$newlink =($row['tweet_url_prefix']) . $newtweetarray[$linkkey].($row['tweet_url_postfix']);	
}
//echo $newlink; exit;
if($row['shorten_bit']=='b'){
$short = make_bitly_url($newlink,$user['bitlyusername'],$user['bitlyapikey'],'json');


if($short==''){
$short =	get_tiny_url($newlink);
}
}
else{
$short =	$newlink;
}
	if(strpos(strtolower( $short), 'error')!==FALSE ){
		$short = '';
	}	
	if(strpos(strtolower($short), '<link>')!==FALSE ){
		$short = '';
	}
	
	if(is_array($short)){
			$short = str_replace('Array', '', $short);
			}
$status_message = trim( $status_message).' '.$short;

if($row['user_id']>1){
$status_message = $status_message. '';}
$status_message = trim($status_message);
	$status_message = str_replace('# ', ' ', $status_message);
	$status_message = str_replace(array('\r', '\n'), '', $status_message);	
	
//	echo 	$status_message; echo strlen($status_message);
$status = $connection->upload('statuses/update_with_media', array('status' => $status_message, 'media[]' => get_web_page($imageurl)));
	//print_r($status); echo 'anu';
	$qu4 = 'update feeds set  current_tweet = now() where id = '.$row['id'];
	$ru4 = mysqli_query($conn,$qu4) or die(mysqli_error($conn));


}
//else if( ((($row['tweet_image']=='' && $imageurl=='' ) || $row['search_image']=='n')) && strlen($status_message)>10 &&  $containsignorewords==false  ){
else if ((($row['tweet_image']=='' && $row['search_image']=='n') || $row['only_if_image']=='n') && strlen($status_message)>20 &&  
$containsignorewords==false && $duplicate==0 ){
	
	$content = $connection->get('account/verify_credentials');
		if(strpos($row['tweet_url_postfix'], 'http://')!==FALSE || strpos($row['tweet_url_postfix'], 'www')!==FALSE ){
		$row['tweet_url_postfix'] = urlencode($row['tweet_url_postfix']);
	}	
	if($row['tweet_url_filters']!==''){
	$newlink =($row['tweet_url_prefix']) . urlencode( str_replace($row['tweet_url_filters'],'',$newtweetarray[$linkkey])) .($row['tweet_url_postfix']);
	}
	
	else{
	$newlink =($row['tweet_url_prefix']) . $newtweetarray[$linkkey].($row['tweet_url_postfix']);	
	}
	//echo $newlink; exit;
	if($row['shorten_bit']=='b'){
	$short = make_bitly_url($newlink,'satyamtechnologies','R_c8edf1909e7a9fc5da60d6b2eeeb1ec7','json');
	
	
	if($short==''){
	$short =	get_tiny_url($newlink);
	}
	}
	else{
	$short =  $newlink;	
	}
	
	if(strpos(strtolower( $short), 'error')!==FALSE ){
		$short = '';
	}	
	if(strpos(strtolower($short), '<link>')!==FALSE ){
		$short = '';
	}
	if(is_array($short)){
			$short = str_replace('Array', '', $short);
			}
	$status_message =trim( $status_message).' '.$short;
	
	
//	$status_message = $status_message. ' | '.utf8_encode('satyam.').' tech';
	$status_message = trim($status_message);
	$status_message = str_replace('# ', ' ', $status_message);
	$status_message = str_replace(array( '\r', '\n'), '', $status_message);

//	echo 	$status_message; echo strlen($status_message);
	$status = $connection->post('statuses/update', array('status' => $status_message));
	//	print_r($status); echo 'anu';
	$qu4 = 'update feeds set  current_tweet = now() where id = '.$row['id'];
	$ru4 = mysqli_query($conn,$qu4) or die(mysqli_error($conn));	
}
else{
	
	if($duplicate>0){
	$status_log = 'Error';
	$log = "Duplicate Tweet at our end, rejected.";			
	}
	else{
	$status_log = 'Error';
	$log = "The feed does not fulfill the criteria specified.";		
	}
//	$qu4 = 'update feeds set  current_tweet = now() where id = '.$row['id'];
//	$ru4 = mysqli_query($conn,$qu4) or die(mysqli_error($conn));	
	
	
}
if(($row['tweet_image']!=='' || $row['search_image']=='y'||$row['onlyifimage']=='y' ) && 
get_web_page($imageurl)==FALSE ){
	$log     = "Image is not found";
	if($row['posts_ignore_words']!=='' && $containsignorewords){
	$log     .= ' or Post contains one of the ignore words - '.$row['posts_ignore_words'];		
	}
	}
	else{
	if($containsignorewords!==false){
	$log     = 'Post contains one of the ignore words - '.$row['posts_ignore_words'];		
	}
	else if(strlen($status_message)<20){
		$log     = 'Post length is only ' .strlen($status_message). 'characters.' ;	
	}
	}
//echo 'anu';
//print_r($status);

if(!isset($newlink)){
	
	$newlink = $newtweetarray[$linkkey];
	
}

if(isset($status->id)){
	$status_log = 'Success';
	$log = '<a href="https://twitter.com/'.$user[0]['username'].'/status/'.$status->id.'" target="_new" >https://twitter.com/'.
	$user[0]['username'].'/status/'.$status->id.'</a>';
}

else{
 $status_log = isset($status_log)?$status_log: 'Error'	;
 $log =  isset($log)?$log:'An Error Occured';
}

if(isset($status->errors) ){
	$errors = $status->errors;
	$error= $errors[0];	//print_r($error);
	$error = $error->message;
	$log = $error;
}

$log=	mysqli_escape_string($conn,$log);
$newtweetarray[$titlekey] = mysqli_escape_string($conn,$newtweetarray[$titlekey]);
$status_log = mysqli_escape_string($conn,$status_log);
$status_message = mysqli_escape_string($conn,$status_message);
$imageurl = mysqli_escape_string($conn,$imageurl);
$newlink = mysqli_escape_string($conn,$newlink);
$feedurl = mysqli_escape_string($conn,$feedurl);
if($row['current_tweet_count']==''){$row['current_tweet_count']=0;}
$qlog = "insert into history  (	feed_id,user_id, title, log_status, log, current_page, current_tweet_count, status_message, image_url, link, feedurl) values({$row['id']}, 
{$row['user_id']}, '{$newtweetarray[$titlekey]}', '{$status_log}', '{$log}',{$page}, {$row['current_tweet_count']},
 '{$status_message}', '{$imageurl}','{$newlink}' , '{$feedurl}' )";

$qr = mysqli_query($conn,$qlog) or die(mysqli_error($conn));

//print_r($status);

echo $status_log.'<br />'; 
echo $status_message.'<br />';

unset($status);

}
?>