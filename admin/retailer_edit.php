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

	$pn = (int)$_GET['pn'];

	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
			unset($errors);
			$errors = array();

			$retailer_id		= (int)getPostParameter('rid');
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
			
			
			
			//Replace space character with '-'
			$slug = str_replace(" ","-",$slug_value);
			$query_check_slug = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE retailer_slug = '$slug'");
			$row_retailer = mysql_fetch_assoc($query_check_slug);

			if($row_retailer['retailer_id'] == $retailer_id)
			{
				$slug = $slug_value;
			}
			else{
				$pos     = "";
				$pos_val = "";
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
			}
			
			if (!($rname && $url && $status && $slug_value)) //$cashback && $cashback_sign
			{
				$errors[] = "Please ensure that all fields marked with an asterisk are complete";
			}
			else
			{
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
				else
				{
					$program_id = 0;
				}

				if (isset($cashback) && $cashback != "" && !is_numeric($cashback))
				{
					$errors[] = "Please enter correct cashback value (digits only)";
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
			if( (isset($_FILES['image_I'])&& ($_FILES['image_I']['name']!="")) || (isset($_FILES['image_II']) && ($_FILES['image_II']['name']!="")) || (isset($_FILES['image_III'])&& ($_FILES['image_III']['name']!="")) )
			{
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
						
						if(isset($_FILES['image_I'])&& $_FILES['image_I']['name']!="")
						{
							if (!(in_array($extension_I, $allowedExts) && in_array($_FILES["image_II"]["type"], $valid_mime_types)))
							{
						  		$errors[] = 'Invalid Image_I type - only gif, jpeg, jpg or png are allowed to upload';
							}
							
							if($_FILES["image_II"]["size"] > 5242880){
								$errors[] = 'Allowed maximum file size is 5MB.';
							}
						}
						//For Image-II
						if(isset($_FILES['image_II'])&& $_FILES['image_II']['name']!="")
						{
							$temp_II = explode(".", $_FILES["image_II"]["name"]);
							$extension_II = end($temp_II);
				
							if (!(in_array($extension_II, $allowedExts) && in_array($_FILES["image_II"]["type"], $valid_mime_types)))
							{
						  		$errors[] = 'Invalid Image_II type - only gif, jpeg, jpg or png are allowed to upload';
							}
							
							if($_FILES["image_II"]["size"] > 5242880){
								$errors[] = 'Allowed maximum file size is 5MB.';
							}
						}
						//For Image-III
						if(isset($_FILES['image_III'])&& $_FILES['image_III']['name']!="")
						{
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
					}
				}


			if (count($errors) == 0)
			{
				//Image upload functionality in case of image change
				unset($img_upload_arr);
				$img_upload_arr = array();
				
				//Fetch the previous location of retailer images - to unlink in case user upload new images
				if (isset($_GET['id']) && is_numeric($_GET['id']))
				{
					$id	= (int)$_GET['id'];
					$query = "SELECT image_I,image_III,image_III FROM cashbackengine_retailers WHERE retailer_id='$id' LIMIT 1";
					$rs	= smart_mysql_query($query);
					$total = mysql_num_rows($rs);
				}
				if ($total > 0) {
					$row_images = mysql_fetch_array($rs);
				}
				if(isset($_FILES['image_I'])&& $_FILES['image_I']['name']!="")
				{
				if (file_exists("upload/retailer/" . $_FILES["image_I"]["name"]))
					{
						//rename the file and upload
						$temp_I = explode(".", $_FILES["image_I"]["name"]);
						$extension_I = end($temp_I);
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
					$img_upload_arr['image_I']=$loc_image_I ;
					if(file_exists("upload/retailer/".$row_images['image_I']))
						unlink("upload/retailer/".$row_images['image_I']);
				}
				if(isset($_FILES['image_II'])&& $_FILES['image_II']['name']!="")
				{
					//Image_II functionlity - upload
					if (file_exists("upload/retailer/" . $_FILES["image_II"]["name"]))
					{
						//rename the file and upload 
						$temp_II = explode(".", $_FILES["image_II"]["name"]);
						$extension_II = end($temp_II);
						
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
					$img_upload_arr['image_II'] = $loc_image_II ;
					if(file_exists("upload/retailer/".$row_images['image_II']))
						unlink("upload/retailer/".$row_images['image_II']);
				}
				if(isset($_FILES['image_III'])&& $_FILES['image_III']['name']!="")
				{
				//Image_III functionlity - upload
					if (file_exists("upload/retailer/" . $_FILES["image_III"]["name"]))
					{
						//rename the file and upload - pending
						$temp_III = explode(".", $_FILES["image_III"]["name"]);
						$extension_III = end($temp_III);
						
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
					$img_upload_arr['image_III'] = $loc_image_III;
					if(file_exists("upload/retailer/".$row_images['image_III']))
						unlink("upload/retailer/".$row_images['image_III']);
				}
					foreach($img_upload_arr as $img_key=>$img_val)
					{ 
						$str_img.= $img_key.'='.'"'.$img_val.'"'.',';
					}
				
					
				$qry=smart_mysql_query("
						UPDATE cashbackengine_retailers SET 
							title='$rname', 
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
							deal_of_week='$deal_of_week',
							popular_retailer='$popular_retailer', 
							$str_img
							status='$status',
							top_retailer ='$top_retailer',
							is_profile_completed = '0',
							retailer_slug ='$slug'
							WHERE retailer_id='$retailer_id'");
	
				smart_mysql_query("DELETE FROM cashbackengine_retailer_to_category WHERE retailer_id='$retailer_id'");
				if (count($category) > 0)
				{
					foreach ($category as $cat_id)
					{
						$cats_insert_sql = "INSERT INTO cashbackengine_retailer_to_category SET retailer_id='$retailer_id', category_id='$cat_id'";
						smart_mysql_query($cats_insert_sql);
					}
				}
				//For categories on top
				if(count($category_on_top)>0)
				{
					$query = "";
					foreach($category_on_top as $cat_top)
					{
						$query_update_retailer_cat = "UPDATE cashbackengine_retailer_to_category SET category_on_top = 1 WHERE 	retailer_id ='$retailer_id' AND category_id ='$cat_top'";
						smart_mysql_query($query_update_retailer_cat);
					}
				}

				smart_mysql_query("DELETE FROM cashbackengine_retailer_to_country WHERE retailer_id='$retailer_id'");
				if (count($country) > 0)
				{
					foreach ($country as $country_id)
					{
						$countries_insert_sql = "INSERT INTO cashbackengine_retailer_to_country SET retailer_id='$retailer_id', country_id='$country_id'";
						smart_mysql_query($countries_insert_sql);
					}
				}

				header("Location: retailers.php?msg=updated");
				exit();
			}
			else
			{
				$errormsg = "";
				foreach ($errors as $errorname)
					$errormsg .= "&#155; ".$errorname."<br/>";
			}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id	= (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	$title = "Edit Retailer";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Retailer</h2>

	<?php if ($total > 0) {
		$row = mysql_fetch_array($rs);
	?>

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
	<?php } ?>

      <form action="" method="post" name="form1" enctype="multipart/form-data">
        <table class="editRetailerAdminAction" cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
         </tr>
         <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="rname" id="rname" value="<?php echo $row['title']; ?>" size="62" class="textbox" /></td>
		</tr>
		 <tr>
            <td width="30%" valign="middle" align="right" class="tb1"><span class="req">* </span>Slug:</td>
			 <td width="70%" valign="top">
				<input type="text" name="slug" id="slug" value="<?php echo $row['retailer_slug']; ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Affiliate Network:</td>
            <td valign="top">
			<select class="textbox2" id="network_id" name="network_id" onchange="javascript:hiddenDiv('network_id','program_id')" <?php if ($network_id) echo "style='display: block;'"; ?>>
				<option value="">-----------------------</option>
				<?php
					$sql_affs = smart_mysql_query("SELECT * FROM cashbackengine_affnetworks WHERE status='active' ORDER BY network_name ASC");
					while ($row_affs = mysql_fetch_array($sql_affs))
					{
						if ($row['network_id'] == $row_affs['network_id']) $selected = " selected=\"selected\""; else $selected = "";
						echo "<option value=\"".$row_affs['network_id']."\"".$selected.">".$row_affs['network_name']."</option>";
					}
				?>	
			</select>
			</td>
			</tr>
          <tr id="program_id" <?php if ($row['network_id'] == 0) { ?>style="display: none;" <?php } ?>>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Program ID:</td>
            <td valign="top"><input type="text" name="program_id" value="<?php echo $row['program_id']; ?>" size="15" class="textbox" /><span class="note">Program ID from affiliate network</span></td>
			</tr>
          <tr>
            <td valign="top" align="right" class="tb1">Category:</td>
            <td valign="top">
				<div class="scrollbox">
				<?php
					unset($retailer_cats);
					$retailer_cats = array();

					$sql_retailer_cats = smart_mysql_query("SELECT category_id FROM cashbackengine_retailer_to_category WHERE retailer_id='$id'");

					if (mysql_num_rows($sql_retailer_cats) > 0)
					{
						while ($row_retailer_cats = mysql_fetch_array($sql_retailer_cats))
						{
							$retailer_cats[] = $row_retailer_cats['category_id'];
						}
					}

					$cc = 0;
					$allcategories = array();
					$allcategories = CategoriesList(0);
					foreach ($allcategories as $category_id => $category_name)
					{
						$cc++;
						if (is_array($retailer_cats) && in_array($category_id, $retailer_cats)) 
						{
							$checked = 'checked="checked"';
							
						} else 
						{
							$checked = '';
						}

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
				<?php
				$query = "SELECT 
								c.category_id,
								c.name,
								rc.category_on_top 
							FROM  cashbackengine_categories 
								AS c INNER JOIN cashbackengine_retailer_to_category AS rc
								ON c.category_id=rc.category_id 
							WHERE retailer_id = $id";	

				$result = smart_mysql_query($query);
				while($top_cat = mysql_fetch_assoc($result))
				{
					$top_cat_id = $top_cat['category_id'];
					$top_cat_name = $top_cat['name'];
					$cat_on_top = $top_cat['category_on_top']; // Check using cat_top to make the checkbox check
					
					if($cat_on_top==1){
						$checkbox_state = 'checked';
					}
					else 
					{
						$checkbox_state = '';
					}
					if($top_cat_id)
					{
						?><div id="div_cat_<?php echo $top_cat_id;?>">
						<input type="checkbox" value="<?php echo $top_cat_id;?>" 
						name="category_on_top[]" <?php echo $checkbox_state;?>><?php echo $top_cat_name;?>
						<span class="note">Select to show in top</span>
							</div><?php 
					}
				}
				?> 
				</div>
				</td>
			</tr>
          <tr>
            <td valign="top" align="right" class="tb1">Country:</td>
            <td valign="top">
				<div class="scrollbox">
				<?php

					unset($retailer_countries);
					$retailer_countries = array();

					$sql_retailer_countries = smart_mysql_query("SELECT country_id FROM cashbackengine_retailer_to_country WHERE retailer_id='$id'");		
					
					while ($row_retailer_countries = mysql_fetch_array($sql_retailer_countries))
					{
						$retailer_countries[] = $row_retailer_countries['country_id'];
					}

					$cc = 0;
					$sql_country = "SELECT * FROM cashbackengine_countries ORDER BY name ASC";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							$cc++;
							if (is_array($retailer_countries) && in_array($row_country['country_id'], $retailer_countries)) $checked = 'checked="checked"'; else $checked = '';

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
			<!-- comment functionality is the previous one in which the url was prodided by the user to be shown as icons-->
			<!-- <tr>
			<td valign="middle" align="right" class="tb1">Current Image:</td>
			<td align="left" valign="top"><img src="<?php if (!stristr($row['image'], 'http')) echo "/img/"; echo $row['image']; ?>" style="max-width:88px;max-height:31px" align="left" alt="" title="" border="0" class="imgs" /></td>
			</tr>
			<tr>
			<td valign="middle" align="right" class="tb1">Image URL:</td>
			<td align="left" valign="top"><input type="text" name="image_url" class="textbox" value="<?php echo $row['image']; ?>" size="100" /></td>
			</tr>-->
			
			<!-- Image functionality (by this functionality the user upload three different images of different size)-->
				<tr>
					<td valign="middle" align="right" class="tb1">Current Image_I:</td>
					<td align="left" valign="top"><img src="<?php if (!stristr($row['image_I'], 'http')) echo 'upload/retailer/'.$row['image_I']; ?>"  style="max-width:120px;max-height:60px" align="left" alt="" title="" border="0" class="imgs" /></td>
				</tr>
	          <tr>
	            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Image I:</td>
	            <td valign="top"><input type="file" name="image_I" class="textbox"  size="100" />(120x60)</td>
	          </tr>
	          <tr>
					<td valign="middle" align="right" class="tb1">Current Image_II:</td>
					<td align="left" valign="top"><img src="<?php if (!stristr($row['image_II'], 'http')) echo 'upload/retailer/'.$row['image_II']; ?>"  style="max-width:150px;max-height:50px" align="left" alt="" title="" border="0" class="imgs" /></td>
				</tr>
	          <tr>
	            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Image II:</td>
	            <td valign="top"><input type="file" name="image_II" class="textbox" size="100" />(300x100)</td>
	          </tr>
	          <tr>
					<td valign="middle" align="right" class="tb1">Current Image_III:</td>
					<td align="left" valign="top"><img src="<?php if (!stristr($row['image_III'], 'http')) echo 'upload/retailer/'.$row['image_III']; ?>"  style="max-width:88px;max-height:31px" align="left" alt="" title="" border="0" class="imgs" /></td>
				</tr>
	          <tr>
	            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Image III:</td>
	            <td valign="top"><input type="file" name="image_III" class="textbox" size="100" />(88x31)</td>
	          </tr>
            <tr>
            <td valign="top" align="right" class="tb1"><span class="req">* </span>URL:</td>
            <td nowrap="nowrap" valign="top">
				<input type="text" name="url" id="url" value="<?php echo $row['url']; ?>" size="96" class="textbox" /><br/>
				<font color="#838383">Please DO NOT forget to add '<font color="#E72085">{USERID}</font>' to your affiliate link to track members.</font>
			</td>
			</tr>
			<?php
					if (strstr($row['old_cashback'], '%'))
					{
						$old_cashback = str_replace('%','',$row['old_cashback']);
						$old_selected1 = "";
						$old_selected2 = "selected";
					}
					elseif (strstr($row['old_cashback'], 'points'))
					{
						$old_cashback = str_replace('points','',$row['old_cashback']);
						$old_selected1 = $old_selected2 = "";
						$old_selected3 = "selected";
					}
					else
					{
						$old_cashback = $row['old_cashback'];
						$old_selected2 = $old_selected3 = "";
						$old_selected1 = "selected";
					}
			?>
            <tr>
            <td valign="middle" align="right" class="tb1">Old Cashback:</td>
            <td valign="top">
				<input type="text" name="old_cashback" id="old_cashback" value="<?php echo $old_cashback; ?>" size="4" class="textbox" />
				 <select name="old_cashback_sign">
					<option value="currency" <?php echo $old_selected1; ?>><?php echo SITE_CURRENCY; ?></option>
					<option value="%" <?php echo $old_selected2; ?>>%</option>
					<option value="points" <?php echo $old_selected3; ?>>points</option>
				</select>
			</td>
			</tr>
			<?php
					if (strstr($row['cashback'], '%'))
					{
						$cashback = str_replace('%','',$row['cashback']);
						$selected1 = $selected3 = "";
						$selected2 = "selected";
					}
					elseif (strstr($row['cashback'], 'points'))
					{
						$cashback = str_replace('points','',$row['cashback']);
						$selected1 = $selected2 = "";
						$selected3 = "selected";
					}
					else
					{
						$cashback = $row['cashback'];
						$selected2 = $selected3 = "";
						$selected1 = "selected";
					}
			?>
            <tr>
				<td valign="middle" align="right" class="tb1">Cashback:</td>
				<td valign="top">
					<input type="text" name="cashback" id="cashback" value="<?php echo $cashback; ?>" size="4" class="textbox" />
					 <select name="cashback_sign">
						<option value="currency" <?php echo $selected1; ?>><?php echo SITE_CURRENCY; ?></option>
						<option value="%" <?php echo $selected2; ?>>%</option>
						<option value="points" <?php echo $selected3; ?>>points</option>
					</select>
				</td>
			</tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Description:</td>
				<td valign="top"><textarea name="description" id="editor1" cols="75" rows="8" class="textbox2"><?php echo stripslashes($row['description']); ?></textarea></td>
            </tr>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor1' );
				</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Contidions:</td>
				<td valign="top"><textarea name="conditions" cols="112" rows="2" class="textbox2"><?php echo strip_tags($row['conditions']); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="112" rows="2" class="textbox2"><?php echo strip_tags($row['meta_description']); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo $row['meta_keywords']; ?>" size="96" class="textbox" /></td>
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
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo ($row['end_date'] != "0000-00-00 00:00:00") ? substr($row['end_date'], 0, 10) : ""; ?>" size="10" maxlength="10" class="textbox" />&nbsp; <input type="text" name="end_time" id="end_time" value="<?php echo ($row['end_date'] != "0000-00-00 00:00:00") ? substr($row['end_date'], -8, 5) : ""; ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Featured?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="featured" value="1" <?php if ($row['featured'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Store of the Week?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="deal_of_week" value="1" <?php if ($row['deal_of_week'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
             <tr>
				<td valign="middle" align="right" class="tb1">Popular Retailer?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="popular_retailer" value="1" <?php if ($row['popular_retailer'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            
            <tr>
				<td valign="middle" align="right" class="tb1">Top Retailer - in all retailers?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="top_retailer" value="1" <?php if ($row['top_retailer'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
					<option value="expired" <?php if ($row['status'] == "expired") echo "selected"; ?>>expired</option>
				</select>
			</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="rid" id="rid" value="<?php echo (int)$row['retailer_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit">
				<input type="submit" class="submit" name="update" id="update" value="Update Retailer" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='retailers.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
              </td>
            </tr>
          </table>
      </form>

      <?php }else{ ?>
			<p align="center">Sorry, no retailer found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<script>
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