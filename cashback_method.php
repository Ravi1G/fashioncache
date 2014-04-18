<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");
	
	if(isset($_POST['submit_payment_method']))
	{
		$payment_method	=	mysql_real_escape_string(getPostParameter('cashback_method_radio'));
		$venmo_username	=	mysql_real_escape_string(getPostParameter('veno_username'));
		$paypal_email	=	mysql_real_escape_string(getPostParameter('paypal_email'));
		$address		=	mysql_real_escape_string(getPostParameter('address'));
		$city			=	mysql_real_escape_string(getPostParameter('city'));
		$state			=	mysql_real_escape_string(getPostParameter('state'));
		$country		=	mysql_real_escape_string(getPostParameter('country'));
		$zip			=	mysql_real_escape_string(getPostParameter('zip'));
		
		unset($errs);
		$errs = array();
		//Validations
		if((isset($payment_method)) && ($payment_method=="check" ))
		{
			if(!($address && $city && $state && $zip && $country))
			{
				$errs[]= "Please fill in all required fields";	
			}
			elseif(!is_numeric($zip))
			{
				$errs[] = "Zip code must be numeric";
			}
		}
		elseif((isset($payment_method)) && ($payment_method=="paypal"))
		{
			if(isset($paypal_email) && ($paypal_email==""))
			{
				$errs[] = 'paypal email is mendatory';
			}
			elseif(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $paypal_email))
			{
				$errs[] = 'Incorrect email format'; 
			}
		}
		elseif( (isset($payment_method)) && ($payment_method == "venmo" ))
		{
			if(isset($venmo_username) && ($venmo_username==""))
			{
				$errs[]= 'Usename is mendatory';
			}
			
		}
		
		if(count($errs) == 0)
		{
			$sql = "INSERT INTO cashbackengine_cashback_method SET 
			venmo_username	=	'$venmo_username',
			cashback_method	=	'$payment_method',
			paypal_email	=	'$paypal_email',
			address			=	'$address',
			city			=	'$city',
			state			=	'$state',
			country			=	'$country',
			zip				=	'$zip',
			user_id			=	'$userid'	";
			
			echo $sql;exit;

			if (smart_mysql_query($sql))
			{
				
				header("Location: cashback_method.php?msg=added");
				exit();
			}
		}
	}

?>

<?php 
///////////////  Page config  ///////////////
	$PAGE_TITLE = "Cashback";

	require_once ("inc/header.inc.php");
?>	

<?php 
// Section to display errors
	if (count($errs) > 0)
	{
		foreach ($errs as $errorname) { $allerrors .= '<li><div class="errorMessage">' . $errorname . '</div></li>'; }
		?>							
		 <div class="errorMessageContainer">
			<div class="leftContainer errorIcon"></div>
			<div class="leftContainer">	
				<!-- For Single line error, add singleError class -->
			   <ul class="standardList errorList <?php if (count($errs) < 2) { ?>singleError<?php } ?>"> 	             			
				  <?php echo $allerrors;?>															
			   </ul>
			</div>              
			<div class="cb"></div>			
		</div>					
		<?php 
	}
?>
				
<form method='post' action=''>
	<div>
		Your Cashback Method
	</div>
	<div>
		Select Method
		<div>
			<input type="radio" id='venmo_radio' class="radioBtnClass" name="cashback_method_radio" value='venmo' checked='checked'>Venmo
			<input type="radio" id='paypal_radio' class="radioBtnClass" name="cashback_method_radio" value='paypal'>Paypal
			<input type="radio" id='check_radio' class="radioBtnClass" name="cashback_method_radio" value='check'>Check
		</div>
		<div id='venmo_div'>Venmo Container
			<div>
				Venmo UserName: <input type='text' name='venmo_username' value="<?php echo $venmo_username;?>">
			</div>
		</div>
		<div id='paypal_div'>Paypal Container
			email:<input type='text' name='paypal_email' value="<?php echo $paypal_email;?>">
		</div>
		<div id='check_div'>Check Container
			<div>
				<div>
					Address: <br>
					<input type='text' name='address' value="<?php echo $address;?>">
				</div>
				<div>
					City: <br>
					<input type='text' name='city' value="<?php echo $city;?>">
				</div>
				<div>
					State: <br>
					<input type='text' name='state' value="<?php echo $state;?>">
				</div>
				<div>
					Country: <br>
					<input type='text' name='country' value="<?php echo $country;?>">
				</div>
				<div>
					Zip: <br>
					<input type='text' name='zip' value="<?php echo $zip;?>">
				</div>
			</div>
		</div>
		<div>
			<input type='submit' name='submit_payment_method' value='Save'>
		</div>
	</div>
</form>

<!-- Script to hide and show the selected option -->	
<script>
$('input:radio').change(function(){
   	payment_method=$('input[name=cashback_method_radio]:checked').val();
   	if(payment_method=='venmo')
   	{
   		$("#venmo_div").show();
   	 	$("#paypal_div").hide();
   	 	$("#check_div").hide();
   	}
   	else if(payment_method=='paypal')
   	{
   		$("#venmo_div").hide();
   	 	$("#paypal_div").show();
   	 	$("#check_div").hide();
   	}
   	else if(payment_method=='check')
   	{
   		$("#venmo_div").hide();
   	 	$("#paypal_div").hide();
   	 	$("#check_div").show();
   	}  
});          

</script>