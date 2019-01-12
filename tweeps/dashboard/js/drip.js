$(document).ready(function () {
	
    $("p.helptext").hide();
    $("#preview").css({'background-color':'#ffffff', 'border':'none'});

    $("#feedname").click(function(){
         $("#pfeedname").toggle();
    });

    $("#rssurl").click(function(){
         $("#prssurl").toggle();
  		 $('#prssurl').css({
		    position: 'absolute',
		    top: '162px'
		});         
    });

    $("#pagenum").click(function(){
         $("#ppagenum").toggle();
  		 $('#ppagenum').css({
		    position: 'absolute',
		    top: '135px'
		});         
    });
    
    $("#postrotation").click(function(){
         $("#ppostrotation").toggle();
  		 $('#ppostrotation').css({
		    position: 'absolute',
		    top: '223px'
		});       
    });  
    $("#tweettextfilters").click(function(){
         $("#ptweettextfilters").toggle();
		 $('#ptweettextfilters').css({
		    position: 'absolute',
		    top: '506px'
		});        
    });	
    $("#postsignorewords").click(function(){
         $("#ppostsignorewords").toggle();
		 $('#ppostsignorewords').css({
		    position: 'absolute',
		    top: '575px'
		});        
    });
    $("#tweeturlfilters").click(function(){
         $("#ptweeturlfilters").toggle();
		 $('#ptweeturlfilters').css({
		    position: 'absolute',
		    top: '600px'
		});        
    });	
    $("#tweeturlprefix").click(function(){
         $("#ptweeturlprefix").toggle();
		 $('#ptweeturlprefix').css({
		    position: 'absolute',
		    top: '680px'
		});        
    });
    $("#tweeturlpostfix").click(function(){
         $("#ptweeturlpostfix").toggle();
		 $('#ptweeturlpostfix').css({
		    position: 'absolute',
		    top: '725px'
		});        
    });	
    

            $("#showpreview").click(function(event){
            	
			 	feedurl = $("#trssurl").val();
				pagefrom = $("#pagefrom").val();
				tweettext = $("#tweettext").val();  
				searchimage = $('#searchimage').prop('checked') ;   
				tweeturlfilters = $("#tweeturlfilters").val();
				tweettextfilters = $("#ttweettextfilters").val();				
				postsignorewords =  $("#tpostsignorewords").val();
				tweeturlprefix =  $("#ttweeturlprefix").val();	
				tweeturlpostfix =   $("#ttweeturlpostfix").val();  
				tweetimage = 	$("#tweetimage").val();  
				shortenbit = $("#shortenbit").val(); 
				uiduser = $("#uiduser").val(); 				
				showpreview = true;
				if(searchimage==true){searchimage='y';}
               $.post( 
                  "drip.php",
                  { showpreview:showpreview, rssurl: feedurl, pagefrom: pagefrom,tweettext:tweettext, searchimage:searchimage, tweeturlprefix : tweeturlprefix , tweeturlpostfix : tweeturlpostfix , tweeturlfilters:tweeturlfilters, tweettextfilters:tweettextfilters,postsignorewords:postsignorewords, tweetimage:tweetimage,shortenbit:shortenbit, uiduser:uiduser},
                  function(data) {
              	     $("#preview").css({'background': '#FCFBE3', 'border': '1px solid #FCFFCE'});
                     $('#preview').html(data);
                  }
               );
					
            }); 
            
      $("#trssurl").change(function(){
      			feedname = $("#tfeedname").val();
			 	feedurl = $("#trssurl").val();
				pagefrom = $("#pagefrom").val();
				tweettext = $("#tweettext").val();  
				searchimage = $('#searchimage').prop('checked') ;  
				onlyifimage = $('#onlyifimage').prop('checked') ; 				 
				tweeturlfilters = $("#ttweeturlfilters").val();
				postsignorewords =  $("#tpostsignorewords").val();
				tweeturlprefix =  $("#ttweeturlprefix").val();	
				tweeturlpostfix =   $("#ttweeturlpostfix").val();  
				tweetimage = 	$("#tweetimage").val();  
				shortenbit = $("#shortenbit").val(); 
				uiduser = $("#uiduser").val(); 
				testurl = true; 
				if(feedurl.indexOf("http://") =='-1'&& feedurl.indexOf("https://") =='-1' && feedurl!=''){
					feedurl = 'http://'+feedurl;
					$("#trssurl").val(feedurl);
				}

				pathArray  = feedurl.split( '/' );
				ylength = pathArray.length-1;
				if(ylength< 3 && feedurl.indexOf("?") =='-1'&& feedurl!=''&& feedurl!='http://'&& feedurl!='https://'){
				feedurl = feedurl+'/feed/';	
				$("#trssurl").val(feedurl);
				}
				else if(ylength== 3 && feedurl.substr(feedurl.length - 1)=='/' && feedurl.indexOf("?") =='-1'&& feedurl!=''&& feedurl!='http://'&& feedurl!='https://'){
				feedurl = feedurl+'feed/';	
				$("#trssurl").val(feedurl);	
				}
			//	alert(feedurl + ' '+ feedurl.indexOf("?") + ' '+ylength);
				/*	lastparturl = pathArray[ylength-1];
				if(lastparturl==''  && lastparturl.indexOf("?") =='-1' ){
					lastparturl = pathArray[ylength-2];			
				} //alert(lastparturl);
				 if((lastparturl.indexOf("www") !='-1'||lastparturl.indexOf(".in") !='-1')||lastparturl.indexOf(".com") !='-1')||lastparturl.indexOf(".net") !='-1') && lastparturl.indexOf("?") =='-1' ){
				 	feedurl = feedurl+'/feed/';
				 	feedurl = feedurl.replace('//feed/','/feed/');
				 	$("#trssurl").val(feedurl);

				 } */
				 				 	
				 //	alert(feedurl);
               $.post( 
                  "drip.php",
                  { testurl:testurl, rssurl: feedurl, pagefrom: pagefrom,tweettext:tweettext, tweeturlprefix : tweeturlprefix, searchimage:searchimage,onlyifimage:onlyifimage,tweeturlfilters:tweeturlfilters, postsignorewords:postsignorewords, tweetimage:tweetimage,shortenbit:shortenbit,uiduser:uiduser},
                  function(data) {
                     data  = $.trim(data);
                     if(data=='valid'){ 
                     	$('#pmrssurl').html('<span class="valid"><img src="images/valid.png" width="15" height="15"/> The RSS URL is valid.</span>');
                     }
                     else if(data=='invalid'){
 						$('#pmrssurl').html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/> The RSS URL is invalid. Try Again.</span>');   
                     }
                     if(feedname=='' && data=='valid'){
                     	$("#pmfeedname").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Feednam cannot be blank. </span>');
                     }                
                  }
               );          
    });
	
    $("#tfeedname").change(function(){
    	if($("#tfeedname").val()==''){
    	$("#pmfeedname").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Feednam cannot be blank. </span>');
         }  	
     	else if($("#tfeedname").val().length<6){
		$("#pmfeedname").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>Feednam should be Six charracters long. </span>');              	
  		}
  		else{
		$("#pmfeedname").html('<span class="valid"><img src="images/valid.png" width="15" height="15"/>The Feednam is Okay. </span>');                	
  		}        
    });	
	
    $("#pagefrom").change(function(){
    	if($("#pagefrom").val()==''||$("#pageto").val()==''){
    	$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Page Numbers cannot be blank. </span>');
         }  	
     	else if($.isNumeric ($("#pagefrom").val())==false||$.isNumeric ($("#pageto").val())==false ){
		$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Page Numbers should be Numeric. </span>');          
  		}
  		else if(parseInt($("#pagefrom").val())>parseInt($("#pageto").val())){
    	$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>From Page cannot be greater than To Page. </span>');
         }
  		else{
		$("#pmpagenum").html('<span class="valid"><img src="images/valid.png" width="15" height="15"/>The Page Numbers are Okay. </span>');                	
  		}        
    });	
	
    $("#pageto").change(function(){
    	if($("#pagefrom").val()==''||$("#pageto").val()==''){
    	$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Page Numbers cannot be blank. </span>');
         }  	
     	else if($.isNumeric ($("#pagefrom").val())==false||$.isNumeric ($("#pageto").val())==false ){
		$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Page Numbers should be Numeric. </span>');              	
  		}
  		else if(parseInt($("#pagefrom").val())>parseInt($("#pageto").val())){
    	$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>From Page cannot be greater than To Page. </span>');
         }  		
  		else{
		$("#pmpagenum").html('<span class="valid"><img src="images/valid.png" width="15" height="15"/>The Page Numbers are Okay. </span>');                	
  		}        
    });		          
				          
    $("textarea#tweettext").change(function(){ 
    	if($("textarea#tweettext").val()==''){
    	$("#pmtweettext").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Message cannot be blank. </span>');
         }  	
     	else if(($("textarea#tweettext").val()).indexOf('{')<0 ||($("textarea#tweettext").val()).indexOf('}')<0  ){ 
		$("#pmtweettext").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>Message does not include RSS variables. Ex. {title}. </span>');              	
  		}
  		else{
		$("#pmtweettext").html('<span class="valid"><img src="images/valid.png" width="15" height="15"/>The Message is Okay. </span>');                	
  		}        
    });	
	
    $("#tweeturl").change(function(){ 
    	if($("#tweeturl").val()==''){
    	$("#pmtweeturl").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The URL cannot be blank. </span>');
         }  	
     	else if(($("#tweeturl").val()).indexOf('{')<0 ||($("#tweeturl").val()).indexOf('}')<0  ){ 
		$("#pmtweeturl").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>URL does not include RSS variables. Ex. {link}. </span>');              	
  		}
  		else{
		$("#pmtweeturl").html('<span class="valid"><img src="images/valid.png" width="15" height="15"/>The URL is Okay. </span>');                	
  		}        
    });
	
	$('#addfeedform').submit(function(){
		flag = true;
		if($("#tfeedname").val()==''){
    	$("#pmfeedname").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Feednam cannot be blank. </span>');
    	flag= false;
         }  	
     	else if($("#tfeedname").val().length<6){
		$("#pmfeedname").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>Feednam should be Six charracters long. </span>');  
 		flag= false;            	
  		}
 		if($("#trssurl").val()==''){
    	$("#pmrssurl").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The RSS URL cannot be blank. </span>');
    	flag= false;
         }  	
     	else if($("#trssurl").val().length<6){
		$("#pmrssurl").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The RSS URL is invalid. </span>');  
 		flag= false;            	
  		} 		
  		
     	if($("#pagefrom").val()==''||$("#pageto").val()==''){
    	$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Page Numbers cannot be blank. </span>');
    	flag= false;
         }  	
     	else if($.isNumeric ($("#pagefrom").val())==false||$.isNumeric ($("#pageto").val())==false ){
		$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Page Numbers should be Numeric. </span>');  
 		flag= false;       
  		}
  		else if(parseInt($("#pagefrom").val())>parseInt($("#pageto").val())){
    	$("#pmpagenum").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>From Page cannot be greater than To Page. </span>');
    	flag= false;
         }
    	if($("textarea#tweettext").val()==''){
    	$("#pmtweettext").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The Message cannot be blank. </span>');
    	    	flag= false;
         }  	
     	else if(($("textarea#tweettext").val()).indexOf('{')<0 ||($("textarea#tweettext").val()).indexOf('}')<0  ){ 
		$("#pmtweettext").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>Message does not include RSS variables. Ex. {title}. </span>');  
		    	flag= false;            	
  		} 
    	if($("#tweeturl").val()==''){
    	$("#pmtweeturl").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>The URL cannot be blank. </span>');
    			    	flag= false;
         }  	
     	else if(($("#tweeturl").val()).indexOf('{')<0 ||($("#tweeturl").val()).indexOf('}')<0  ){ 
		$("#pmtweeturl").html('<span class="invalid"><img src="images/invalid.png" width="15" height="15"/>URL does not include RSS variables. Ex. {link}. </span>');    
				    	flag= false;          	
  		}
		  
		  
  	       errortx = $( '#pmrssurl').text(); 
  	      // alert(errortx.indexOf("invalid"));
  	       if(errortx.indexOf("invalid") !='-1'){
  	       //	alert($('#pmrssurl').text())	;
  	       		flag= false; 
  	       }
  	       
  	       previewtext = $( '#preview').text(); 
  	       
  	       if(previewtext.indexOf("No Preview") !='-1'){
  	       	$( '#preview').css('color','red');
  	       		flag= false; 
  	       }
  	       
    	 /*      if(previewtext==''){
  	       	$( '#preview').html('<h2 style="color:red;">No Preview</h2> Please generate a preview first.');
  	       		flag= false; 
  	       }	  */     
  	       
  	     //  alert(previewtext);
  	       
  	       return flag; 		
	})	;
	

				  
});

