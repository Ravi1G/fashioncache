  window.fbAsyncInit = function() {
    FB.init({
      appId      : FACEBOOK_APPID, 
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });
    
    
	FB.Event.subscribe('auth.authResponseChange', function(response) 
	{
 	 if (response.status === 'connected') 
  	{
  		document.getElementById("message").innerHTML +=  "<br>Connected to Facebook";
  		//SUCCESS
  		
  	}	 
	else if (response.status === 'not_authorized') 
    {
    	document.getElementById("message").innerHTML +=  "<br>Failed to Connect";

		//FAILED
    } else 
    {
    	document.getElementById("message").innerHTML +=  "<br>Logged Out";

    	//UNKNOWN ERROR
    }
	});	
	
    };
    
   	function fbLogin()
	{
		FB.login(function(response) {
		   if (response.authResponse)
		   	{
		    	getUserInfo();	
  			} 
			else 
  			{
  	    	 console.log('User cancelled login or did not fully authorize.');
   			}
		 },{scope: 'email,offline_access,publish_stream,user_birthday,user_location'});
	}

  function getUserInfo() {
	    FB.api('/me', function(response) {
	    	$.ajax({
				type: "POST",
				url: "fblogin.php",
				data: { username: response.username, email: response.email ,id:response.id,first_name:response.first_name,last_name:response.last_name,link:response.link ,action: 'signupFB'}
				})
				.done(function( msg ) {
					if(CLOSE_POPUP==1){
						parent.location.href = SITE_URL+'index.php';
						return false;
					}
					else if(msg=='myaccount.php' && CLOSE_POPUP==0){ // NEED TO BE UPDATED SITE_URL
						window.location=SITE_URL+'index.php';
					}else if(msg='index.php?msg=welcome' && CLOSE_POPUP==0){
						window.location=SITE_URL+'index.php?msg=welcome';
					}else if(msg='index.php' && CLOSE_POPUP==0){
						window.location=SITE_URL+'index.php';
					}
				});		
    });
    }
	function getPhoto()
	{
	  FB.api('/me/picture?type=normal', function(response) {
		  var str="<br/><b>Pic</b> : <img src='"+response.data.url+"'/>";
	  	  document.getElementById("status").innerHTML+=str;
	  });
	}
	function Logout()
	{
		FB.logout(function(){document.location.reload();});
	}

  // Load the SDK asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
