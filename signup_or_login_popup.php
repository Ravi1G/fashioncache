<?php 
session_start();
include_once 'inc/config.inc.php';?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
    <!--<![endif]-->
    <head>
          <title><?php echo SITE_TITLE;?></title>
          <?php include 'inc/common.php';?>          
    </head>
    <?php 
    if(!isset($_POST['action']))
	{
		$section	= "signup";
		$email		= "";
	}
    ?>
    
     <?php	// session variable to store information which will be used in signup_or_login page 
    	if( isset($_POST['action']) && $_POST['action'] != "" && $_POST['action']=='signup')
    	{
    		$close_popup = 1;
    		$_SESSION['email']		=	$_POST['email'];
    		$_SESSION['password']	=	$_POST['password'];
    		$_SESSION['action']		=	'signup';
    		$section				=	'signup';
    		$email					=	$_POST['email'];
    	}
    	else if( isset($_POST['action']) && $_POST['action'] != "" && $_POST['action']=='login')
    	{
    		$close_popup = 1;
    		$_SESSION['email']		=	$_POST['email'];
    		$_SESSION['password']	=	$_POST['password'];
    		$_SESSION['action']		=	'login';
    		$section				=	'login';
    		$email					=	$_POST['email'];
    		if($_SESSION['retailer_url'])
			{
				$redirect_url = $_SESSION['retailer_url'];
				//unset($_SESSION['retailer_url']);
			}
			else 
			{
				$redirect_url = "index.php";	
			}
			?>
			<script>
				parent.$.colorbox.close();
			</script>
			<?php 
    	}
    	else{
    		$close_popup = 0;
    	}
    	
    	if(isset($_SESSION['goRetailerID']) && $_SESSION['goRetailerID']!="")
    	{
    		$retailerId		=	$_SESSION['goRetailerID'];
    		$query			=	mysql_query("SELECT image,image_I,image_II,image_III, cashback FROM cashbackengine_retailers WHERE retailer_id=$retailerId");
    		$row			=	mysql_fetch_assoc($query);
    		$retailerImg	=	$row['image_I'];
    		$cashback		=	$row['cashback'];		
    	}
    ?>  
    <body>
		<div class="signUpPopup">
			<div class="popupLogoSection">
				<img src="<?php echo SITE_URL.'admin/upload/retailer/'.$retailerImg;?>" alt="Logo"/>
			</div>
			<div class="popupLeadLine">
				Join FREE now and get <span><?php echo DisplayCashback($cashback);?> Cash Back</span> on Fashion Cache orders.
			</div>
			<!-- Block for login -->
			 <div id="loginBlock" class="popupFormSection <?php if($section=='signup'){ echo 'hidden';}?>">
				<form action="" method="post" id="frmlogin">
					<div class="leftAligned inputArea">
						<div class="standardInputBox"><input type="text" name="email" placeholder="Email" value="<?php echo $email;?>"/></div>
						<div class="standardInputBox">						
						<input type="password" name="password" placeholder="Password (6-12 Characters)"/> 						
						</div>
						<div class="cb"></div>
						<div class="alreadyMember">
							<div class="shopNowBotton siteButton popupFormSectionButton">
								<a href="#" id="submitloginform">
									<span>Login &#x003E;</span>
								</a>
							</div>
							<div class="colorLinkNew">
							<a class="colorLink" href="" id='signupLink'>New Member?</a>
							</div>
							<div class="cb"></div>
						</div>
					</div>
					<div class="rightAligned logoArea">
						<img src="img/fashionCacheNewLogo.png" alt="Fashion Cache"/>
					</div>
					<div class="cb"></div>
					<input type='hidden' name='action' value='login'>
				</form>
			</div>
			
			<!-- Block for Signup -->
			<div id="signupBlock" class="popupFormSection <?php if($section=='login'){ echo 'hidden';}?>">
				<form action="" id="frmsignup" method="post">
					<div class="leftAligned inputArea">
						<div class="standardInputBox"><input type="text" name="email" placeholder="Email" value="<?php echo $email;?>"/></div>
						<div class="standardInputBox">						
						<input type="password" name="password"  placeholder="Password (6-12 Characters)"/> 						
						</div>
						<div class="cb"></div>
						<div class="alreadyMember">
							<div class="shopNowBotton siteButton popupFormSectionButton">
								<a href="#" id="submitsignupform">
									<span>Sign Up &#x003E;</span>
								</a>
							</div>
							<div class="colorLinkNew">
								<a class="colorLink" href="" id='loginLink'>Already a Member?</a>
							</div>
							<div class="cb"></div>
							</div>
					</div>
					<div class="rightAligned logoArea">
						<img src="img/fashionCacheNewLogo.png" alt="Fashion Cache"/>
					</div>
					<div class="cb"></div>
					<input type='hidden' name='action' value='signup'>
				</form>
			</div>
			
			<div class="popupEndingLines popupEndingLinesMargins">
				<div>By becoming a member, you agree to our <a class="colorLink" href="#">Terms &#x0026; Conditions</a>.</div>
				<div>&#x00A9; 1998-2014 Fashion Cache. All rights reserved. <a class="colorLink" href="#">Privacy Policy</a></div>
			</div>			
		</div>
		<script>
			$(function(){
				<?php if($close_popup){ ?>
					parent.location.href = '<?php echo SITE_URL;?>signup_or_login.php';
				<?php } ?>
				$('#loginLink').click(function(e){
					$("#loginBlock").removeClass('hidden');
					$("#signupBlock").addClass('hidden');
					<?php $section = "";?>
					return false;
				});
				$('#signupLink').click(function(e){
					$("#loginBlock").addClass('hidden');
					$("#signupBlock").removeClass('hidden');
					<?php $section = "";?>
					return false;
				});
				$('#submitsignupform').click(function(e){
					e.preventDefault();
					$('#frmsignup').submit();
				});
				$('#submitloginform').click(function(e){
					e.preventDefault();
					$('#frmlogin').submit();
				});
			});
		</script>
		
	   <script type="text/javascript" src="<?php echo SITE_URL?>js/plugins.js"></script>
	   <script type="text/javascript" src="<?php echo SITE_URL?>js/main.js"></script>
	   </body>
</html>		