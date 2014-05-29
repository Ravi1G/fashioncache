<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	set_time_limit(120);
	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");
	$url = "http://";

	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
			unset($errors);
			$errors = array();
	 
			$network_id			= (int)getPostParameter('network_id');
			$program_id			= trim(getPostParameter('program_id'));
			$category			= array();
			$category			= $_POST['category_id'];
			$country			= array();
			$country			= $_POST['country_id'];
			$rname				= mysql_real_escape_string(getPostParameter('rname'));
			$slug_value			= mysql_real_escape_string(getPostParameter('slug'));
			
			$img				= mysql_real_escape_string(trim($_POST['image_url']));
			$url				= mysql_real_escape_string(trim($_POST['url']));
			$old_cashback		= mysql_real_escape_string(getPostParameter('old_cashback'));
			$old_cashback_sign	= mysql_real_escape_string(getPostParameter('old_cashback_sign'));
			$cashback			= mysql_real_escape_string(getPostParameter('cashback'));
			$cashback_sign		= mysql_real_escape_string(getPostParameter('cashback_sign'));
			$description		= mysql_real_escape_string($_POST['description']);
			$conditions			= mysql_real_escape_string(nl2br(getPostParameter('conditions')));
			$meta_description	= mysql_real_escape_string(getPostParameter('meta_description'));
			$meta_keywords		= mysql_real_escape_string(getPostParameter('meta_keywords'));
			$end_date			= mysql_real_escape_string(getPostParameter('end_date'));
			$end_time			= mysql_real_escape_string(getPostParameter('end_time'));
			$retailer_end_date	= $end_date." ".$end_time;
			$featured			= (int)getPostParameter('featured');
			$deal_of_week		= (int)getPostParameter('deal_of_week');
			$popular_retailer	= (int)getPostParameter('popular_retailer');
			$status				= mysql_real_escape_string(getPostParameter('status'));
			$top_retailer		= mysql_real_escape_string(getPostParameter('top_retailer'));
			
			$category_on_top	= array();
			$category_on_top	= $_POST['category_on_top'];
			
			$r_img_I			= $_FILES['image_I']['name'];
			$r_img_II			= $_FILES['image_II']['name'];
			$r_img_III			= $_FILES['image_III']['name'];
			
			$newFilename  = '';
			$thumbSmallerFilename = '';
			$thumbMediumFilename = '' ;

			//Retailer Image functionality
			
			
			// no image url provided
			if ($img == "")
			{
				$img = "noimg.gif";
			}
			// image url provided
			else {/*
				require_once("../inc/Zebra_Image.php");
				
				// download the image from Web
				$imgDownload = file_get_contents($img);
				$originalFilename = pathinfo($imageExternalUrl, PATHINFO_FILENAME); 
				$newFilename  = $originalFilename . '_' . md5(uniqid()) . '.jpg';
				$status = file_put_contents("upload/$newFilename", $imgDownload);
				
				if (status != false) {
	    			$image = new Zebra_Image();
	    			$imageMedium = new Zebra_Image();
	    		
	    			// indicate a source image (a GIF, PNG or JPEG file)
	    			$image->source_path = "upload/$newFilename";
	    			$imageMedium->source_path = "upload/$newFilename";
	    		
		    		// indicate a target image
				    // note that there's no extra property to set in order to specify the target 
				    // image's type -simply by writing '.jpg' as extension will instruct the script 
				    // to create a 'jpg' file
				    $newParts = explode('.', $newFilename);
					$newName = $newParts[0];
					$newExt = $newParts[1];
				
					// smaller thumbnail
					$thumbSmallerFilename = $newName . '_' . md5(uniqid()) . '.' . $newExt;
				    $image->target_path = "upload/$thumbSmallerFilename";
			    
				    // some additional properties that can be set
				    // read about them in the documentation
				    $image->preserve_aspect_ratio = true;
				    $image->enlarge_smaller_images = false;
				    $image->preserve_time = true;
				
					// resize the image to exactly 100x100 pixels by using the "crop from center" method
				    // (read more in the overview section or in the documentation)
				    //  and if there is an error, check what the error is about
				    $image->resize(120, 60, ZEBRA_IMAGE_NOT_BOXED);
			    
				    // medium thumbnail
			   		$thumbMediumFilename = $newName . '_' . md5(uniqid()) . '.' . $newExt;
			    	$imageMedium->target_path = "upload/$thumbMediumFilename";
			    
				    // some additional properties that can be set
				    // read about them in the documentation
				    $imageMedium->preserve_aspect_ratio = true;
				    $imageMedium->enlarge_smaller_images = false;
				    $imageMedium->preserve_time = true;
				
					// resize the image to exactly 100x100 pixels by using the "crop from center" method
				    // (read more in the overview section or in the documentation)
				    //  and if there is an error, check what the error is about
				    $imageMedium->resize(300, 100, ZEBRA_IMAGE_NOT_BOXED);
					
					// adding to db later in this file
				}*/
			}

			if (!($rname && $url && $r_img_I && $r_img_II && $r_img_III && $slug_value)) //$cashback && $cashback_sign
			{
				$errors[] = "Please ensure that all fields marked with an asterisk are complete";
			}
			else
			{
				//Validation for r_img_I,r_img_II,r_img_II

					if ($_FILES["image_I"]["error"] > 0)
		  			{
						$error[]= $_FILES["image_I"]["error"];
					}
					else if($_FILES["image_II"]["error"] > 0)
					{
						$error[]= $_FILES["image_II"]["error"];
					}
					else if($_FILES["image_III"]["error"] > 0 )
					{
						$error[]= $_FILES["image_III"]["error"];
					}
					else
					{
						$valid_mime_types = array(
							"image/gif",
							"image/jpeg",
							"image/jpg",
							"image/pjpeg",
							"image/x-png",
							"image/png",
						);
						//For Image I
						$allowedExts = array("gif", "jpeg", "jpg", "png");
						$temp_I = explode(".", $_FILES["image_I"]["name"]);
						$extension_I = end($temp_I);
			
						if (!(in_array($extension_I, $allowedExts) && in_array($_FILES["image_II"]["type"], $valid_mime_types)))
						{
					  		$errors[] = 'Invalid Image_I type - only gif, jpeg, jpg or png are allowed to upload';
						}
						
						if($_FILES["image_I"]["size"] > 5242880){
							$errors[] = 'Allowed maximum file size is 5MB.';
						}
						
						//For Image-II
						$temp_II = explode(".", $_FILES["image_II"]["name"]);
						$extension_II = end($temp_II);
			
						if (!(in_array($extension_II, $allowedExts) && in_array($_FILES["image_II"]["type"], $valid_mime_types)))
						{
					  		$errors[] = 'Invalid Image_II type - only gif, jpeg, jpg or png are allowed to upload';
						}
						
						if($_FILES["image_II"]["size"] > 5242880){
							$errors[] = 'Allowed maximum file size is 5MB.';
						}
						
						//For Image-III
						$temp_III = explode(".", $_FILES["image_III"]["name"]);
						$extension_III = end($temp_III);
			
						if (!(in_array($extension_III, $allowedExts) && in_array($_FILES["image_III"]["type"], $valid_mime_types)))
						{
					  		$errors[] = 'Invalid Image_III type - only gif, jpeg, jpg or png are allowed to upload';
						}
						
						if($_FILES["image_III"]["size"] > 5242880){
							$errors[] = 'Allowed maximum file size is 5MB.';
						}
					}
				
				if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://')
				{
					$errors[] = "Enter correct url format, enter the 'http://' or 'https://' statement before your link";
				}
				elseif ($url == 'http://' || $url == 'https://')
				{
					$errors[] = "Please enter correct URL";
				}

				if (isset($network_id) && $network_id != "")
				{
					if (!$program_id || $program_id == "")
						$errors[] = "Please fill in Program ID field";
				}

				if (isset($cashback) && $cashback != "" && !is_numeric($cashback))
				{
					$errors[] = "Please enter correct cashback value (digits only)";
				}

				if (isset($end_date) && $end_date != "")
				{
					if (strtotime($end_date) < strtotime("now"))
					{
						$errors[] = "Sorry, that expiration date has already passed. Please enter a date in the future.";
					}
				}
			}

			if (isset($cashback) && is_numeric($cashback))
			{
				switch ($old_cashback_sign)
				{
					case "currency":	$old_cashback_sign = ""; break;
					case "%":			$old_cashback_sign = "%"; break;
					case "points":		$old_cashback_sign = "points"; break;
				}

				switch ($cashback_sign)
				{
					case "currency":	$cashback_sign = ""; break;
					case "%":			$cashback_sign = "%"; break;
					case "points":		$cashback_sign = "points"; break;
				}

				if ($old_cashback != "") $retailer_old_cashback	= $old_cashback.$old_cashback_sign;
				$retailer_cashback = $cashback.$cashback_sign;
			}
			else
			{
				$old_cashback = "";
				$retailer_cashback = "";
			}


			if (count($errors) == 0)
			{
				//Image_I functionlity - upload
					if (file_exists("upload/retailer/" . $_FILES["image_I"]["name"]))
					{
						//rename the file and upload
						$name =	$temp_I[0].'1.';
						$name = $name.$extension_I;
						while(file_exists("upload/retailer/" . $name)) //If file with new name already exist then change the name
						{
							$temp_I = explode(".", $name);
							$name = $temp_I[0].'1.';
							$name = $name.$extension_I;
						}
						move_uploaded_file($_FILES["image_I"]["tmp_name"],
						"upload/retailer/" . $name);
				     	$loc_image_I = $name;
				     	
					}
					else
					{
						move_uploaded_file($_FILES["image_I"]["tmp_name"],
						"upload/retailer/" . $_FILES["image_I"]["name"]);
				     	$loc_image_I = $_FILES["image_I"]["name"];
					}
				
				//Image_II functionlity - upload
					if (file_exists("upload/retailer/" . $_FILES["image_II"]["name"]))
					{
						//rename the file and upload 
						$name =	$temp_II[0].'1.';
						$name = $name.$extension_II;
						while(file_exists("upload/retailer/" . $name)) //If file with new name already exist then change the name
						{
							$temp_II = explode(".", $name);
							$name = $temp_II[0].'1.';
							$name = $name.$extension_II;
						}
						move_uploaded_file($_FILES["image_II"]["tmp_name"],
						"upload/retailer/" . $name);
				     	$loc_image_II = $name;
					}
					else
					{
						move_uploaded_file($_FILES["image_II"]["tmp_name"],
						"upload/retailer/" . $_FILES["image_II"]["name"]);
				     	$loc_image_II = $_FILES["image_II"]["name"];
					}				
				
				//Image_III functionlity - upload
					if (file_exists("upload/retailer/" . $_FILES["image_III"]["name"]))
					{
						//rename the file and upload - pending
						$name =	$temp_III[0].'1.';
						$name = $name.$extension_III;
						while(file_exists("upload/retailer/" . $name)) //If file with new name already exist then change the name
						{
							$temp_III = explode(".", $name);
							$name = $temp_III[0].'1.';
							$name = $name.$extension_III;
						}
						move_uploaded_file($_FILES["image_III"]["tmp_name"],
						"upload/retailer/" . $name);
				     	$loc_image_III = $name;
					}
					else
					{
						move_uploaded_file($_FILES["image_III"]["tmp_name"],
						"upload/retailer/" . $_FILES["image_III"]["name"]);
				     	$loc_image_III = $_FILES["image_III"]["name"];
					}
					
			//Slug functionality starts
				$pos     = "";
				$pos_val = "";
				//Replace space character with '-'
				$slug = str_replace(" ","-",$slug_value);

				//Check for the exisint slug if any matches then put number at the end of slug
				$query_check_exisiting_slug = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE retailer_slug = '$slug'");
				$row_retailer = mysql_fetch_assoc($query_check_exisiting_slug);
				
				while(mysql_num_rows($query_check_exisiting_slug)!==0)
				{
					$pos = strpos($row_retailer['retailer_slug'], "-");
					if($pos)
					{
						$pieces    = explode("-", $row_retailer['retailer_slug']);
						$count = count($pieces);
						for($i=0;$i<$count;$i++)
						{
							$val = $val.$pieces[$i].'-';
						}
						$pos_value = $pieces[$count-1];
						if(!$pos_value)
						{
							$pos_value = 1;
						}
						else
						{
							$pos_value = intval($pos_value) + 1;
						}
						
						$slug = str_replace(" ","-",$slug_value).'-'.$pos_value;
					}
					else
					{
						$slug = $slug_value.'-1'; 
					}
					
					$query_check_exisiting_slug = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE retailer_slug = '$slug'");
					$row_retailer = mysql_fetch_assoc($query_check_exisiting_slug);
					
				}
					//Slug functionality ends
					
					
					//Get maximum sort order of the retailers who are active in status
					$max= smart_mysql_query("SELECT max(sort_order) as max_order FROM cashbackengine_retailers WHERE status='active'");
					$max_order = mysql_fetch_assoc($max);	 
					$max_val = $max_order['max_order']+1;
					$insert_sql = "
						INSERT INTO cashbackengine_retailers 
						SET title='$rname', 
						network_id='$network_id', 
						program_id='$program_id', 
						url='$url', image='$img', 
						old_cashback='$retailer_old_cashback', 
						cashback='$retailer_cashback', 
						conditions='$conditions', 
						description='$description', 
						meta_description='$meta_description', 
						meta_keywords='$meta_keywords', 
						end_date='$retailer_end_date', 
						featured='$featured',
						popular_retailer='$popular_retailer', 
						deal_of_week='$deal_of_week', 
						status='$status', 
						added=NOW(),
						image_original = '$newFilename',
						image_120x60 = '$thumbSmallerFilename',
						image_300x100 = '$thumbMediumFilename',
						image_I	=	'$loc_image_I',
						image_II =	'$loc_image_II',
						image_III =	'$loc_image_III',
						sort_order = '$max_val',
						top_retailer ='$top_retailer',
						retailer_slug = '$slug' 
					";
					$result = smart_mysql_query($insert_sql);
					$new_retailer_id = mysql_insert_id();

					if (count($category) > 0)
					{
						foreach ($category as $cat_id)
						{
							$cats_insert_sql = "INSERT INTO cashbackengine_retailer_to_category SET retailer_id='$new_retailer_id', category_id='$cat_id'";
							smart_mysql_query($cats_insert_sql);
						}
					}
					
					//For categories on top
					if(count($category_on_top)>0)
					{
						$query = "";
						foreach($category_on_top as $cat_top)
						{
							$query_update_retailer_cat = "UPDATE cashbackengine_retailer_to_category SET category_on_top = 1 WHERE 	retailer_id ='$new_retailer_id' AND category_id ='$cat_top'";
							smart_mysql_query($query_update_retailer_cat);
						}
					}

					if (count($country) > 0)
					{
						foreach ($country as $country_id)
						{
							$countries_insert_sql = "INSERT INTO cashbackengine_retailer_to_country SET retailer_id='$new_retailer_id', country_id='$country_id'";
							smart_mysql_query($countries_insert_sql);
						}
					}

					header("Location: retailers.php?msg=added");
					exit();
			}
			else
			{
				$errormsg = "";
				foreach ($errors as $errorname)
					$errormsg .= "&#155; ".$errorname."<br/>";
			}

	}

	$title = "Add Retailer";
	require_once ("inc/header.inc.php");

?>

    <h2>Add Retailer</h2>

		<script>
		<!--
			function hiddenDiv(id,showid){
				if(document.getElementById(id).value != ""){
					document.getElementById(showid).style.display = ""
				}else{
					document.getElementById(showid).style.display = "none"
				}
			}
		-->
		</script>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div class="error_box"><?php echo $errormsg; ?></div>
	<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
		<div class="success_box">Retailer has been successfully added</div>
	<?php } ?>

      <form action="" method="post" name="form1" enctype="multipart/form-data">
        <table width="100%" cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td width="70%" valign="top"><input type="text" name="rname" id="rname" value="<?php echo getPostParameter('rname'); ?>" size="62" class="textbox" /></td>
          </tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1"><span class="req">* </span>Slug:</td>
			 <td width="70%" valign="top">
				<input type="text" name="slug" id="slug" value="<?php echo getPostParameter('slug'); ?>" size="40" class="textbox" />
			</td>
          </tr>
			<tr>
            <td nowrap="nowrap" width="30%" valign="middle" align="right" class="tb1">Affiliate Network:</td>
            <td width="70%" valign="top">
			<select class="textbox2" id="network_id" name="network_id" onchange="javascript:hiddenDiv('network_id','program_id')" <?php if ($network_id) echo "style='display: block;'"; ?>>
			<option value="">-----------------------</option>
				<?php

					$sql_affs = smart_mysql_query("SELECT * FROM cashbackengine_affnetworks WHERE status='active' ORDER BY network_name ASC");
				
					while ($row_affs = mysql_fetch_array($sql_affs))
					{
						if ($network_id == $row_affs['network_id']) $selected = " selected=\"selected\""; else $selected = "";

						echo "<option value=\"".$row_affs['network_id']."\"".$selected.">".$row_affs['network_name']."</option>";
					}
				?>
			</select>
			</td>
			</tr>
          <tr id="program_id" <?php if (empty($network_id)) { ?>style="display: none;" <?php } ?>>
            <td width="30%" valign="middle" align="right" class="tb1"><span class="req">* </span>Program ID:</td>
            <td width="70%" valign="top"><input type="text" name="program_id" value="<?php echo $program_id; ?>" size="15" class="textbox" /><span class="note">Program ID from affiliate network</span></td>
			</tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1">Category:</td>
            <td width="70%" valign="top">
				<div class="scrollbox">
				<?php

					$cc = 0;
					$allcategories = array();
					$allcategories = CategoriesList(0);
					foreach ($allcategories as $category_id => $category_name)
					{
						$cc++;
						if (is_array($category) && in_array($category_id, $category)) $checked = 'checked="checked"'; else $checked = '';

						if (($cc%2) == 0)
							echo "<div class=\"even\"><input name=\"category_id[]\" class=\"categories\" value=\"".(int)$category_id."\" ".$checked." type=\"checkbox\" category =\"$category_name\">".$category_name."</div>";
						else
							echo "<div class=\"odd\"><input name=\"category_id[]\" class=\"categories\" value=\"".(int)$category_id."\" ".$checked." type=\"checkbox\" category =\"$category_name\">".$category_name."</div>";
					}

				?>
				</div>
			</td>
          </tr>
          
          <tr>
			<td valign="top" align="right" class="tb1">Put on top:</td>
			<td>
			<!-- Div for the categories which are being selected -->
				<div class ="SelectedCats">
				</div>
				</td>
			</tr>
			
         <tr>
            <td width="30%" valign="middle" align="right" class="tb1">Country:</td>
            <td width="70%" valign="top">
				<div class="scrollbox">
				<?php

					$cc = 0;
					$sql_country = "SELECT * FROM cashbackengine_countries WHERE status='active' ORDER BY name ASC";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							$cc++;
							if (is_array($country) && in_array($row_country['country_id'], $country)) $checked = 'checked="checked"'; else $checked = '';

							if (($cc%2) == 0)
								echo "<div class=\"even\"><input name=\"country_id[]\" value=\"".(int)$row_country['country_id']."\" ".$checked." type=\"checkbox\">".$row_country['name']."</div>";
							else
								echo "<div class=\"odd\"><input name=\"country_id[]\" value=\"".(int)$row_country['country_id']."\" ".$checked." type=\"checkbox\">".$row_country['name']."</div>";
						}
					}

				?>
				</div>
			</td>
          </tr>
         <!-- <tr>
            <td valign="middle" align="right" class="tb1">Image URL:</td>
            <td valign="top"><input type="text" name="image_url" class="textbox" value="<?php echo $image_url; ?>" size="100" /></td>
          </tr>-->
          <!-- Image functionality -->
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Image I:</td>
            <td valign="top"><input type="file" name="image_I" class="textbox"  size="100" />(120x60)</td>
            
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Image II:</td>
            <td valign="top"><input type="file" name="image_II" class="textbox" size="100" />(300x100)</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Image III:</td>
            <td valign="top"><input type="file" name="image_III" class="textbox" size="100" />(88x31)</td>
          </tr>
          <tr>
            <td width="30%" valign="top" align="right" class="tb1"><span class="req">* </span>URL:</td>
            <td nowrap="nowrap" width="70%" valign="top">
				<input type="text" name="url" id="url" value="<?php echo $url; ?>" size="100" class="textbox" /> <a id="show_info" href="#sinfo"><img src="images/icons/notice.png" align="absmiddle" /></a>
				<div id="info" style="display: none;">
				<table bgcolor="#F7F7F7" style="border: 1px dotted #EEE; padding: 5px; margin: 5px 0;" width="100%" cellpadding="2" cellspacing="2" border="0" align="left">
					<tr valign="top">
						<td colspan="2" align="left">
							Please DO NOT forget to add '<font color="#E72085">{USERID}</font>' to your affiliate link to track members.<br/><br/>
							Links examples for few popular affiliate networks:
						</td>
					</tr>
					<tr valign="middle">
						<td nowrap="nowrap" align="right"><b>ShareASale</b>:</td>
						<td nowrap="nowrap" align="left"><font color="#30AF08">http://www.shareasale.com/r.cfm?u=zzzzz&b=xxxxx&m=yyyyy</font><font color="#E72085">&afftrack=<b>{USERID}</b></font></td>
					</tr>
					<tr valign="middle">
						<td nowrap="nowrap" align="right"><b>Zanox</b>:</td>
						<td nowrap="nowrap" align="left"><font color="#30AF08">http://ad.zanox.com/ppc/?142171430629117663T</font><font color="#E72085">&zpar0=<b>{USERID}</b></font></td>
					</tr>
					<tr valign="middle">
						<td nowrap="nowrap" align="right"><b>Commission Junction</b>:</td>
						<td nowrap="nowrap" align="left"><font color="#30AF08">http://www.kqzyfj.com/click-2538644-10432491</font><font color="#E72085">?sid=<b>{USERID}</b></font></td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td style="border-top: 1px #CFCFCF solid;" align="left">where <b>afftrack</b>, <b>zpar0</b> and <b>sid</b> - SubID parameters</td>
					</tr>
				</table>
				</div>
			</td>
			</tr>
			<tr>
				<td width="30%" valign="middle" align="right" class="tb1">Old Cashback:</td>
				<td width="70%" valign="top">
					<input type="text" name="old_cashback" id="old_cashback" value="<?php echo getPostParameter('old_cashback'); ?>" size="4" class="textbox" /> 
					<select name="old_cashback_sign">
						<option value="currency" <?php if ($old_cashback_sign == "currency") echo "selected='selected'"; ?>><?php echo SITE_CURRENCY; ?></option>
						<option value="%" <?php if ($old_cashback_sign == "%") echo "selected='selected'"; ?>>%</option>
						<option value="points" <?php if ($old_cashback_sign == "points") echo "selected='selected'"; ?>>points</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="30%" valign="middle" align="right" class="tb1">Cashback:</td>
				<td width="70%" valign="top">
					<input type="text" name="cashback" id="cashback" value="<?php echo getPostParameter('cashback'); ?>" size="4" class="textbox" /> 
					<select name="cashback_sign">
						<option value="currency" <?php if ($cashback_sign == "currency") echo "selected='selected'"; ?>><?php echo SITE_CURRENCY; ?></option>
						<option value="%" <?php if ($cashback_sign == "%") echo "selected='selected'"; ?>>%</option>
						<option value="points" <?php if ($cashback_sign == "points") echo "selected='selected'"; ?>>points</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Description:</td>
				<td valign="top"><textarea name="description" id="editor1" cols="75" rows="8" class="textbox2"><?php echo getPostParameter('description'); ?></textarea></td>
            </tr>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor1' );
				</script>
			<tr>
				<td valign="middle" align="right" class="tb1">Conditions:</td>
				<td valign="top"><textarea name="conditions" cols="112" rows="2" class="textbox2"><?php echo getPostParameter('conditions'); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="112" rows="2" class="textbox2"><?php echo getPostParameter('meta_description'); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo getPostParameter('meta_keywords'); ?>" size="115" class="textbox" /></td>
            </tr>
			<script>
				$(function() {
			        $('#end_date').calendricalDate();
			        $('#end_time').calendricalTime({
						minTime: {hour: 0, minute: 0},
						maxTime: {hour: 23, minute: 59},
						timeInterval: 30
					})
				});
			</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Expiry Date:</td>
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo getPostParameter('end_date'); ?>" size="10"  maxlength="10" class="textbox" />&nbsp; <input type="text" name="end_time" id="end_time" value="<?php echo getPostParameter('end_time'); ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Featured?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="featured" value="1" <?php if (getPostParameter('featured') == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Store of the Week?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="deal_of_week" value="1" <?php if (getPostParameter('deal_of_week') == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Popular Retailer?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="popular_retailer" value="1" <?php if (getPostParameter('popular_retailer') == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
             <tr>
				<td valign="middle" align="right" class="tb1">Top Retailer - in all retailers?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="top_retailer" value="1" <?php if ($row['top_retailer'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Status:</td>
				<td valign="middle">
					<select name="status">
						<option value="active" <?php if ($status == "active") echo "selected"; ?>>active</option>
						<option value="inactive" <?php if ($status == "inactive") echo "selected"; ?>>inactive</option>
						<option value="expired" <?php if ($status == "expired") echo "selected"; ?>>expired</option>
					</select>
				</td>
            </tr>
            <tr>
				<td align="center" colspan="2" valign="bottom">
					<input type="hidden" name="action" id="action" value="add">
					<input type="submit" class="submit" name="add" id="add" value="Add Retailer" />
				</td>
            </tr>
          </table>
      </form>

	<script>
	$("#show_info").click(function () {
	  $("#info").show("slow");
	});
	$(".categories").click(function(){
		if($(this).prop('checked')) {
			var cat_id = $(this).val();
			var cat = $(this).attr("category");
			$(".SelectedCats").append("<div id=div_cat_"+cat_id+">"+
					"<input type='checkbox' name='category_on_top[]' value="+cat_id+">"+cat+"<span class='note'>Select to show in top</span></div>");
		} 
		else{
			var cat_id = $(this).val();
			var cat = $(this).attr("category");
			$("#div_cat_"+cat_id).remove();
		}
	});
	</script>


<?php require_once ("inc/footer.inc.php"); ?>