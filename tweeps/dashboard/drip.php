<?php



function singletweet(){

	$linkkey = 'link';
	$categorykey = 'category'; 
	$titlekey = 'title';

	include_once("config.php");
	include_once("includes/twitteroauth.php");
	include_once("includes/functions.php");
	include 'includes/bitly.php';
	$conn=dbcon();

	$feedurl = $_POST['rssurl'];
	$page_from = $_POST['pagefrom'];
	$tweet_text = $_POST['tweettext'];
	$tweeturlprefix = $_POST['tweeturlprefix'];
	$tweeturlpostfix = $_POST['tweeturlpostfix'];
	$tweeturlfilters =  $_POST['tweeturlfilters'];
	$tweettextfilters = $_POST['tweettextfilters'];
	$postsignorewords = $_POST['postsignorewords'];
	$tweetimage = $_POST['tweetimage'];
	$shortenbit = $_POST['shortenbit'];
	$onlyifimage = false;
	$searchimage = $_POST['searchimage'];
	$user_id = $_POST['uiduser'];
	
	$quser_id = "select bitlyusername, bitlyapikey from users where id = {$user_id} limit 1";
	$result_id = mysqli_query($conn,$quser_id) or die(mysqli_error($conn));	
	
	$bitlyusername = mysqli_result($result_id, 0,'bitlyusername' );
	$bitlyapikey = mysqli_result($result_id, 0,'bitlyapikey' );	
	
	$error = '';
	$feedurl = str_replace('{page}',$page_from,$feedurl);
	$feed = get_web_page($feedurl);
	
	//print_r($_POST);
	if($feed!=''){
	$xml = @simplexml_load_string($feed, "SimpleXMLElement", LIBXML_NOCDATA);
//	echo 'anu'.$xml; exit;
	}
	if($feed==''){
		$log = "The Feed Cannot be Fetched. An Empty Result was obtained.";
	}
	else if($xml===FALSE){
		$log = "The XML is not valid. So the Articles cannot be fetched.";				
	}
	else{	
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);//print_r($array['entry']);
	$array = $array['channel']['item'];	
	if(empty($array) || count($array)<1){ $array = json_decode($json,TRUE); $array = $array['entry'];} //print_r($array); 
	$newtweetarray = $array[0];// print_r($newtweetarray); 
	//echo $json;
	if(strpos($newtweetarray['description'], 'src')!==FALSE){
$imgs = explode('src="',$newtweetarray['description']);
 $imgs = explode('"',$imgs[1]) ;
  $imgs = $imgs[0]	;
 $newtweetarray['imageurl'] =$imgs;
  }	
  
	if(is_array($newtweetarray[$titlekey])){
	$newtweetarray[$titlekey] = recursiveFind($newtweetarray[$titlekey], $titlekey);
	if(count($newtweetarray[$titlekey]>0))	{
		$newtweetarray[$titlekey] = $newtweetarray[$titlekey][0];
	}
}  
 // print_r($newtweetarray);
	if(strlen($newtweetarray[$titlekey])>60){
	
	$newtitle = str_split($newtweetarray[$titlekey], 60);
	$newtweetarray[$titlekey] = $newtitle[0].'..';
	} //print_r($newtweetarray[$titlekey]);
	

	
	$status_message = str_ireplace("{".$titlekey."}", $newtweetarray[$titlekey], $tweet_text) ;
	//echo $status_message; //exit;
	$categories = $newtweetarray[$categorykey];
	$hash = '';
	if(!is_array($categories) && $categories!=''){
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
	$categoriesm =	array_iunique($categoriesm);
	foreach($categoriesm as $category){
	$category = str_replace(',','',$category);
	$category = explode(' ',$category);
	if(strpos($hash, $category[0])===FALSE){
	$category = $category[0];
		}
		else{
			$category='';
		}
	if(strtolower($category)!== 'uncategorized'	&& strtolower($category)!== 'your'&& strtolower($category)!== 'useful'&& strtolower($category)!== 'about'&& strtolower($category)!== 'case'&& strtolower($category)!== 'getting'&& strtolower($category)!== 'resources'
	 && $i<3 ){
	if($i<3){
	$hash .= ' #'. $category;
	}
	$i++;
	}
	}
	}
//echo $status_message;
	if(strlen($status_message)>70){
	$hash = explode(' ',$hash); 
	$status_message = str_replace('{'.$categorykey.'}', trim( $hash[0]), $status_message) ;
	} 
	else{ 
	$status_message = str_replace('{'.$categorykey.'}', $hash, $status_message) ;	//echo $status_message;
	}
	if(strlen($status_message)>80){
	$status_message = str_split($status_message, 80);
	$status_message = explode('#',$status_message[0]);
	$status_message = $status_message[0];
	}
		$imageurl = '';

	
	//echo $feedurl;
	//echo $searchimage; exit;
	$xmlFeed=@simplexml_load_file($feedurl);
//echo $onlyifimage;
	if( ( $searchimage=='y'|| $onlyifimage=='y') && $imageurl=='' ){
	//	echo $xmlFeed->channel;
	$imageurl='';
		if( isset($xmlFeed->channel->item)){
	foreach($xmlFeed->channel->item as $item){
//	echo $newtweetarray[$titlekey] .' '. $item->title;
	if($newtweetarray[$titlekey] == $item->title ){ //echo 'hi'; exit;
    $media = $item->children('http://search.yahoo.com/mrss/');
	foreach($media as $image){
 	$aurl = $image->attributes()->url; //echo $aurl; exit;
 	$aurl = explode('?',$aurl);
 $aurl = $aurl[0];
 $aurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl);
 		$size=@getimagesize($aurl);//print_r($size); echo $aurl;
	if($imageurl!=='' && get_web_page($aurl)!==FALSE ){
 	if ((!isset($size[0]) || $size[0]>390 )&&(!isset($size[1]) || $size[1]>250) ){		
 echo	$imageurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl) ;}
	}
	} 
	}
	}
	}
	elseif(isset($xmlFeed->entry)){
	foreach($xmlFeed->entry as $item){
	//echo $newtweetarray[$titlekey] .' '. $item->title;
	if($newtweetarray[$titlekey] == $item->title ){ //echo 'hi'; exit;
    $media = $item->children('http://search.yahoo.com/mrss/');
	foreach($media as $image){
 	$aurl = $image->attributes()->url; //echo $aurl; exit;
 	$aurl = explode('?',$aurl);
 $aurl = $aurl[0];
 $aurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl);
 		$size=@getimagesize($aurl); //print_r($size); echo $aurl;
	if($imageurl!=='' && get_web_page($aurl)!==FALSE  ){
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

		
	//echo $searchimage.' anu';
	//echo $newtweetarray[$linkkey]. ' wrw'; exit;

	if($imageurl == '' && $newtweetarray[$linkkey]!=='' &&  ($searchimage=='y' || $onlyifimage=='y')){
	libxml_use_internal_errors(true);
	$doc = new DomDocument(); //echo get_web_page($newtweetarray[$linkkey]);

//	print_r($newtweetarray);
	$ff = get_web_page($newtweetarray[$linkkey]);
	if($ff!==FALSE && $ff!=''){
	$doc->loadHTML($ff);
	$xpath = new DOMXPath($doc);
	$query = '//*/meta[starts-with(@property, \'og:\')]';
	$queryT = '';
	$metas = $xpath->query($query); //print_r($metas);
	foreach ($metas as $meta) {
    $property = $meta->getAttribute('property');
    $content = $meta->getAttribute('content');
    if($property=='og:image'){
    		$size=@getimagesize($content);//  print_r($size); echo $content;
    		
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
  	$size=@getimagesize($aurl); // print_r($size); echo $aurl;

	
	if(	$imageurl=='' && get_web_page($aurl)!==FALSE ){
 	if ((!isset($size[0]) || $size[0]>390 )&&(!isset($size[1]) || $size[1]>250)){
 	$imageurl = str_replace(array('/i1.wp.com/', 'i2.wp.com/', 'i0.wp.com/'),'',$aurl) ;}
	}  
  
}	
		
	}
		}
//	print_r($imageurl); echo 'ad';
	$imageurl = explode('?',$imageurl);
	$imageurl = $imageurl[0];
	}
	
		if(isset($newtweetarray['imageurl']) && $imageurl==''){
			$imageurl = $newtweetarray['imageurl'];
	}
	
	$containsignorewords = false;
	if($postsignorewords!==''){
	$ignorew = explode(',', $postsignorewords)  ;
	foreach($ignorew as $w){
	if(strpos($status_message, trim($w))!==false){
	$containsignorewords = true;
	}
	}
	}
	
	//echo $tweettextfilters;
	if($tweettextfilters!==''){
	$filters = explode(',',$tweettextfilters);
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
	//echo $imageurl.'as';	

		
	if($imageurl!==''  && $containsignorewords==false && get_web_page($imageurl) && strlen($status_message)>20){


	if(strpos($tweeturlpostfix, 'http://')!==FALSE || strpos($tweeturlpostfix, 'www')!==FALSE ){
		$tweeturlpostfix = urlencode($tweeturlpostfix);
	}
	
		if($tweeturlfilters!==''){
	$newlink =($tweeturlprefix) . urlencode( str_replace($tweeturlfilters,'',$newtweetarray[$linkkey])).$tweeturlpostfix ;
	

	}
	else{
	$newlink =($tweeturlprefix) . $newtweetarray[$linkkey].$tweeturlpostfix;	
	}
//echo $newlink;
	if($shortenbit=='b'){
//	echo $newlink; //exit;
	$short = make_bitly_url($newlink,$bitlyusername,$bitlyapikey,'json');
	if($short==''){
	$short =	get_tiny_url($newlink);
	}
	}
	else{
	$short = $newlink;		
	}
	
	if(strpos(strtolower( $short), 'error')!==FALSE ){
		$short = '';
	}	
	if(strpos(strtolower($short), '<link>')!==FALSE ){
		$short = '';
	}	
	
	$status_message = $status_message.' '.$short;
	$status_message = str_replace('<', '', $status_message);
		$status_message = str_replace('>', '', $status_message);
		$status_message = str_replace('Error', '', $status_message);			
	$status_message = str_replace('# ', '', $status_message);
		
	}
	else if((($tweetimage=='' && $searchimage!==TRUE) || $onlyifimage!==TRUE) && strlen($status_message)>20 &&  
$containsignorewords==false  ){
		if(strpos($tweeturlpostfix, 'http://')!==FALSE || strpos($tweeturlpostfix, 'www')!==FALSE ){
		$tweeturlpostfix = urlencode($tweeturlpostfix);
	}
	if($tweeturlfilters!==''){
	$newlink =($tweeturlprefix) . urlencode( str_replace($tweeturlfilters,'',$newtweetarray[$linkkey])) .($tweeturlpostfix);
	}
	else{
	$newlink =($tweeturlprefix) . $newtweetarray[$linkkey].($tweeturlpostfix);	
	}
	//echo $newlink; exit;
	if($shortenbit=='b'){
	$short = make_bitly_url($newlink,$bitlyusername,$bitlyapikey,'json');
	if($short==''){
	$short =	get_tiny_url($newlink);
	}
	}
	else{
	$short = $newlink;	
	}
	if(strpos(strtolower( $short), 'Error')!==FALSE ){
		$short = '';
	}	
	if(strpos(strtolower($short), '<link>')!==FALSE ){
		$short = '';
	}
	
	
	$status_message = $status_message.' '.$short;
	
	$status_message = str_replace('# ', '', $status_message);	
	}
	else{ 
	$log = "The feed does not fulfill the criteria specified";	
	}
 	if(($postsignorewords!=='' || $search_image==true|| $onlyifimage==true) && get_web_page($imageurl)==FALSE ){
	$log     = "Image is not found";
	if($row['posts_ignore_words']!=='' && $containsignorewords){
	$log     .= ' or Post contains one of the ignore words - '.$postsignorewords;		
	}
	}
	else{
	if($containsignorewords!==false){
	$log     = 'Post contains one of the ignore words - '.$postsignorewords;		
	}
	}
	}
	$status_message = isset($status_message)?$status_message:"";
	$imageurl = isset($imageurl)?$imageurl:"";
		
	$firsttweet = array('status_message'=>$status_message, 'image_url'=> $imageurl, 'error'=>$log);
	
	return $firsttweet;	
}

function testrss(){

	$linkkey = 'link';
	$categorykey = 'category'; 
	$titlekey = 'title';
	
	include_once("config.php");
	include_once("includes/twitteroauth.php");
	include_once("includes/functions.php");
	include 'includes/bitly.php';
	dbcon();

	$feedurl = $_POST['rssurl'];
	$page_from = $_POST['pagefrom'];
	$tweet_text = $_POST['tweettext'];
	$tweeturlprefix = $_POST['tweeturlprefix'];
	$tweeturlpostfix = $_POST['tweeturlpostfix'];
	$tweeturlfilters =  $_POST['tweeturlfilters'];
	$postsignorewords = $_POST['postsignorewords'];
	$tweetimage = $_POST['tweetimage'];
	$searchimage = $_POST['searchimage'];
	$onlyifimage = $_POST['onlyifimage'];
	$error = '';
	$feedurl = str_replace('{page}',$page_from,$feedurl);
	$feed = get_web_page($feedurl, 'r');
	$newtweetarray = array();
	if($feed!=''){
	$xml = @simplexml_load_string($feed, "SimpleXMLElement", LIBXML_NOCDATA);
	if($xml!==false){
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	$array = $array['channel']['item'];	
	if(empty($array) || count($array)<1){ $array = json_decode($json,TRUE); $array = $array['entry'];}
	$newtweetarray = $array[0];
	}		
	}
	return $newtweetarray;
}

function rsshastitle($newtweetarray, $titlekey){
	if(isset($newtweetarray[$titlekey]) && $newtweetarray[$titlekey]!='')	{
		return true;
	}
	else{
		return false;
	}	
}

function rsshascategory($newtweetarray, $categorykey){
	if(isset($newtweetarray[$categorykey]) && $newtweetarray[$categorykey]!='')	{
		return true;
	}
	else{
		return false;
	}	
}

function rsshaslink($newtweetarray, $linkkey){
	if(isset($newtweetarray[$linkkey]) && $newtweetarray[$linkkey]!='')	{
		return true;
	}
	else{
		return false;
	}	
}
 
if(isset($_POST['showpreview']) && isset($_POST['rssurl'])){
$f = singletweet($_POST['rssurl']); //print_r($f);

if($f['error']==''){
echo '<h2 style="color:#06b5f2">Tweet Preview</h2>';
echo '<p>&nbsp;</p>';
echo $f['status_message'] ;
echo '<p>&nbsp;</p>';
if($f['image_url']!==''){
echo '<img src="'.$f['image_url'].'" style="width:475px; height:275px;" />';
}
}
else if($f['error']!==''){
echo '<h2 style="color:red;">No Preview</h2>';
echo $f['error'] ;	
}
}

if(isset($_POST['testurl']) && isset($_POST['rssurl'])){
$f = testrss($_POST['rssurl']);

$titlekey = 'title';
//print_r($f);
if(rsshastitle($f, $titlekey)==true && $_POST['rssurl']!=''){
	echo 'valid';
}
else if($_POST['rssurl']!=''){
	echo 'invalid';	
}
}

?>