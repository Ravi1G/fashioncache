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
	
	if(!isset($_POST['payment_method']))
	{
		$select_radio_method		= "venmo";
	}
	
	//On page load, check the user's record is already in the cashback_method table , if exists then update
	$cashback_id	=	0;
	$query			=	"SELECT * FROM  cashbackengine_cashback_method WHERE user_id ='$userid'";
	$result 		=	smart_mysql_query($query);

	if (mysql_num_rows($result) > 0)
	{
		$cashback_row	=	mysql_fetch_array($result);
		
		$cashback_id	=	$cashback_row['cashback_method_id'];
		$payment_method	=	$cashback_row['cashback_method'];
		$venmo_username	=	$cashback_row['venmo_username'];
		$paypal_email	=	$cashback_row['paypal_email'];
		$address		=	$cashback_row['address'];
		$city			=	$cashback_row['city'];
		$state			=	$cashback_row['state'];
		$country		=	$cashback_row['country'];
		$zip			=	$cashback_row['zip'];
		
		$select_radio_method	=	$cashback_row['cashback_method'];
	}
	//Section to deal with the post after insert or update
	if( isset($_POST['payment_method']) && ($_POST['payment_method']!="") )
	{
		$payment_method	=	mysql_real_escape_string(getPostParameter('payment_method'));
		$venmo_username	=	mysql_real_escape_string(getPostParameter('venmo_username'));
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
				$errs[] = 'Please fill in all required fields';
			}
			elseif(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $paypal_email))
			{
				$errs[] = 'Incorrect email format'; 
			}
		}
		elseif( (isset($payment_method)) && ($payment_method == "venmo" ))
		{
			if(!($venmo_username))
			{
				$errs[]= 'Please fill in all required fields';
			}
		}
		if(($cashback_id!=0) && (count($errs) == 0)){
			//Query to clear the previous record
			$clear_sql = "UPDATE cashbackengine_cashback_method SET 
				venmo_username	=	'',
				cashback_method	=	'',
				paypal_email	=	'',
				address			=	'',
				city			=	'',
				state			=	'',
				country			=	'',
				zip				=	''
				WHERE user_id	=	'$userid'";
			
			smart_mysql_query($clear_sql);
			
			$update_sql = "UPDATE cashbackengine_cashback_method SET 
				venmo_username	=	'$venmo_username',
				cashback_method	=	'$payment_method',
				paypal_email	=	'$paypal_email',
				address			=	'$address',
				city			=	'$city',
				state			=	'$state',
				country			=	'$country',
				zip				=	'$zip'
				WHERE user_id	=	'$userid'";
			
		if (smart_mysql_query($update_sql))
			{
				header("Location: cashback_method.php?msg=2");
				exit();
			}
			
		}
		elseif(($cashback_id==0) && (count($errs) == 0))
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

			if (smart_mysql_query($sql))
			{
				header("Location: cashback_method.php?msg=1");
				exit();
			}
		}
	}
?>

<?php 
//  Page config
	$PAGE_TITLE = "Cashback";
	require_once ("inc/header.inc.php");
?>	


<div class="container standardContainer innerRegularPages">
	<?php 
/* Left SideBar Content */
	require_once("inc/left_sidebar.php");				
?>			
<div class="rightAligned flowContent1">
	<h1>CASH BACK METHOD</h1>
	
<?php 
	// Section to display errors or success message
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
	elseif (isset($_GET['msg']) && is_numeric($_GET['msg']) ) 
	{ ?>
		<div class="errorMessageContainer successMessageContainer">
			<div class="leftContainer errorIcon"></div>
			<div class="leftContainer">	
				<!-- For Single line error, add singleError class -->
			   <ul class="standardList errorList singleError"> 	             			
				  <li>
					<div class="errorMessage">
						<?php
							switch ($_GET['msg'])
							{
								case "1": echo "Your Payment Method has been saved successfully"; break;
								case "2": echo "Your Payment Method has been updated successfully"; break;													
							}
						?>
					 </div>
				  </li>															
			   </ul>
			</div>              
			<div class="cb"></div>			
		</div>		
	<?php } ?>			

	<div class="customTable cashBackMethod">
		<div class="cashBackWays">
			<h3>Select Method: <span class="data"><label for="venmo_radio">Venmo</label> <input type="radio" id='venmo_radio' class="radioBtnClass" name="cashback_method_radio" value='venmo' <?php if($select_radio_method=="venmo"){echo "checked='true'";}?>>
			 <label for="paypal_radio">Pay Pal</label> <input type="radio" id='paypal_radio' class="radioBtnClass radio" name="cashback_method_radio" value='paypal' <?php if($select_radio_method=="paypal"){echo "checked='true'";}?>>
			  <label for="check_radio">Check</label> <input type="radio" id='check_radio' class="radioBtnClass radio" name="cashback_method_radio" value='check' <?php if($select_radio_method=="check"){echo "checked='true'";}?>></span></h3>
		</div>
				
				
<!--  Venmo Section  -->
		<div id='venmo_div' class="cashBackContent <?php if($select_radio_method!="venmo"){echo 'hidden';}?>">
		<form id="frm_venmo" method='post' action=''>
			<div class="row locationPlate">
				<div class="label">User Name<sup class="manadatoryField">*</sup></div>
				<div class="data"><input type='text' name='venmo_username' value="<?php echo $venmo_username;?>"></div>
			</div>
			<div class="allStores forSignUp">
				<a><span id="update_venmo_form">SAVE</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?></a>
			</div>
			<input type="hidden" name="payment_method" value="venmo">
		</form>
			<div class="cb"></div>
		</div>	
				
				
<!--  Paypal Section  -->
				<div id="paypal_div" class="cashBackContent hidden">	
				<form id="frm_paypal" method='post' action=''>							
					<div class="row locationPlate">
						<div class="label">Email<sup class="manadatoryField">*</sup></div>
						<div class="data"><input type='text' name='paypal_email' value="<?php echo $paypal_email;?>"></div>
					</div>
					<div class="allStores forSignUp">
						<span id="update_paypal_form">SAVE</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?>
					</div>
					<input type="hidden" name="payment_method" value="paypal">
				</form>
					<div class="cb"></div>
				</div>	

<!--  Check Section  -->
				<div id="check_div" class="cashBackContent hidden">
				<form id='frm_check' method='post' action=''>								
					<div class="row locationPlate">
						<div class="label">Address<sup class="manadatoryField">*</sup></div>
						<div class="data"><input type='text' name='address' value="<?php echo $address;?>"></div>
					</div>
					<div class="row locationPlate">
						<div class="label">City<sup class="manadatoryField">*</sup></div>
						<div class="data"><input type='text' name='city' value="<?php echo $city;?>"></div>
					</div>							
					<div class="row locationPlate">
						<div class="label">State<sup class="manadatoryField">*</sup></div>
						<div class="data">
							<input type='text' name='state' value="<?php echo $state;?>">
							<!-- <select>
								<option>New York</option>
								<option>Albama</option>
								<option>Grev</option>
								<option>Sauthern</option>
							</select>-->
						</div>
					</div>
					<div class="row locationPlate">
						<div class="label">Country<sup class="manadatoryField">*</sup></div>
						<div class="data">
							<select name="country" id="country">
							<?php
		
									$sql_country = "SELECT * FROM cashbackengine_countries WHERE signup='1' AND status='active' ORDER BY name ASC";
									$rs_country = smart_mysql_query($sql_country);
									$total_country = mysql_num_rows($rs_country);
				
									if ($total_country > 0)
									{
										while ($row_country = mysql_fetch_array($rs_country))
										{
											if(isset($country) && ($country!="") && ($country==$row_country['country_id']))
												{
													echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
												}
											elseif ($row['country'] == $row_country['country_id'])
												{
												echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
												}
											else
											{
												echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
											}
										}
									}
				
								?>
							</select>
						</div>
					</div>
					<div class="row locationPlate">
						<div class="label">Zip Code<sup class="manadatoryField">*</sup></div>
						<div class="data"><input type='text' name='zip' value="<?php echo $zip;?>"></div>
					</div>
					<div class="allStores forSignUp">
						<!-- <input type='submit' name='submit_payment_method' value='Save'>-->
						<span id="update_check_form">SAVE</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?>
					</div>
						<input type="hidden" name="payment_method" value="check">
					</form>
				</div>	
			</div>	
</div>
<div class="cb"></div>
</div>

<script>
	//Submit the form on click on the span
	$("#update_check_form").click(function(){
		$("#frm_check").submit();
	});
	$("#update_paypal_form").click(function(){
		$("#frm_paypal").submit();
	});
	$("#update_venmo_form").click(function(){
		$("#frm_venmo").submit();
	});

	//To show and hide the different sections on click of radio button
	
	$('input:radio').change(function(){
	   	var payment_method=$('input[name=cashback_method_radio]:checked').val();
	   	if(payment_method=='venmo')
	   	{
	   		$("#venmo_div").show();
	   	 	$("#venmo_div").removeClass('hidden');
	   	 	$("#paypal_div").addClass('hidden');
	   	 	$("#check_div").addClass('hidden');
	   	}
	   	else if(payment_method=='paypal')
	   	{	
	   	 	$("#paypal_div").show();
	   	 	$("#paypal_div").removeClass('hidden');
	   		$("#venmo_div").addClass('hidden');
	   	 	$("#check_div").addClass('hidden');
	   	}
	   	else if(payment_method=='check')
	   	{
	   		$("#check_div").show();
	   		$("#check_div").removeClass('hidden');
	   		$("#paypal_div").addClass('hidden');
	   	 	$("#venmo_div").addClass('hidden');
	   	}  
	});
	//After getting post - to maintain the state of different sections
	var method="<?php echo $payment_method;?>";
		if(method=='venmo')
		{
			$('input:radio[id=venmo_radio]').prop('checked', true);
			$("#venmo_div").removeClass('hidden');
	   	 	$("#paypal_div").addClass('hidden');
	   	 	$("#check_div").addClass('hidden');
		}
		else if(method=='paypal')
		{
			$('input:radio[id=paypal_radio]').prop('checked', true);
			$("#paypal_div").removeClass('hidden');
	   		$("#venmo_div").addClass('hidden');
	   	 	$("#check_div").addClass('hidden');
		}
		else if(method=='check')
		{
			$('input:radio[id=check_radio]').prop('checked', true);
			$("#check_div").removeClass('hidden');
	   		$("#paypal_div").addClass('hidden');
	   	 	$("#venmo_div").addClass('hidden');
		}

</script>

<?php require_once ("inc/footer.inc.php"); ?>