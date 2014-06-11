<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

  
session_start();
require_once("../inc/adm_auth.inc.php");
require_once("../inc/config.inc.php");
require_once("./inc/admin_funcs.inc.php");

$id		=	(int)trim($_GET['id']);
$isEdit =	$id==0 ? 0 : 1;
$isPost	=	count($_POST)>0 ? 1 : 0;

if($isEdit)
{
	$title = "Edit Banner";
}
else 
{
	$title = "Add Banner";
}

//first time page load
if(!$isPost){
	$select_option = 'use_url';	
}

//Run in case of edit to fetch advertisement information
if($id && !$isPost)
{
	$fetch_record 	=	mysql_fetch_assoc(smart_mysql_query("SELECT banner_id, image, link, retailer_id, sort_order, bypass_script FROM cashbackengine_banners WHERE banner_id=$id"));
	$id				=	$fetch_record['banner_id'];
	$link			=	$fetch_record['link'];
	$image			=	$fetch_record['image'];
	$retailer_id	=	$fetch_record['retailer_id'];
	$sort_order		=	$fetch_record['sort_order'];
	$bypass_script	=	$fetch_record['bypass_script'];
	$_POST = $fetch_record;
	
	$_POST["existingBannerImage"] = $image;
	
	if(empty($fetch_record)){
		header("Location: banners.php?msg=invalid_banner&msg_type=error");
		exit();
	}
}

if ($isPost)
{
	
	unset($errors);
	$errors = array();
	$retailer_id	= mysql_real_escape_string(getPostParameter('retailer_id'));
	$link			= mysql_real_escape_string(getPostParameter('link'));
	$sort_order		= mysql_real_escape_string(getPostParameter('sort_order'));
	$bypass_script	= (int)getPostParameter('bypass_script');
	$image			= $_FILES['image']['name'];

	if ($isEdit && $retailer_id=='' || $sort_order=='') //run in case of edit
	{
		$errors[] = "Please ensure that all fields marked with an asterisk are complete";
	}
	elseif ($retailer_id=='' || ($image=='' && !$isEdit) || $sort_order=='') //run in case of add
	{
		$errors[] = "Please ensure that all fields marked with an asterisk are complete";
	}
	else
	{	
		//validate banner image
		if ($_FILES["image"]["error"] > 0)
  		{
			$error[]= $_FILES["image"]["error"];
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
			
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $_FILES["image"]["name"]);
			$extension = end($temp);

			if (!(in_array($extension, $allowedExts) && in_array($_FILES["image"]["type"], $valid_mime_types)))
			{
		  		$errors[] = 'Upload valid image.';
			}
			
			if($_FILES["image"]["size"] > 5242880){
				$errors[] = 'Allowed maximum file size is 5MB.';
			}
		}
			 
		//validate banner link
		if (isset($link) && $link!="" && !preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $link))
		{
			$errors[] = "Enter correct link url. (e.g. 'http://abc.com' or 'https://abc.com' or 'http://www.abc.com')";
		}
		if(isset($bypass_script) && $bypass_script!="")
		{
			if($link=="")
			{
				$errors[]="Link is mendatory to bypass the script";
			}
		}
	}
	
	if (count($errors) == 0 && $isPost)
	{
		if (file_exists("upload/" . $_FILES["image"]["name"]))
		{
			//rename the file and upload - pending
			$name =	$temp[0].'1.';
			$name = $name.$extension;
			while(file_exists("upload/" . $name)) //If file with new name already exist then change the name
			{
				$temp = explode(".", $name);
				$name = $temp[0].'1.';
				$name = $name.$extension;
			}
			move_uploaded_file($_FILES["image"]["tmp_name"],
			"upload/" . $name);
	     	$loc="upload/" . $name;
		}
		else
		{
			move_uploaded_file($_FILES["image"]["tmp_name"],
			"upload/" . $_FILES["image"]["name"]);
	     	$loc="upload/" . $_FILES["image"]["name"];
		}
		
		//Update record
		if($id)
		{	
			if($image!="")
			{
				//remove image if one already there for banner while edit		
				$sql=mysql_query("SELECT image FROM cashbackengine_banners WHERE banner_id='$id'");
				$row=mysql_fetch_assoc($sql);
				$image_loc = $row['image'];
			
				if($image_loc!="" && file_exists($image_loc))	
					$img_del_result=unlink($image_loc);	
				//remove block ends
				$sql = "UPDATE cashbackengine_banners  SET sort_order='$sort_order', link='$link', image='$loc', retailer_id='$retailer_id',bypass_script='$bypass_script' WHERE banner_id='$id'";
			}
			else
			{
				$sql = "UPDATE cashbackengine_banners  SET sort_order='$sort_order',link='$link', retailer_id='$retailer_id',bypass_script='$bypass_script' WHERE banner_id='$id'";
			}
			
			if(smart_mysql_query($sql))
			{
				header("Location: banners.php?msg=updated");
				exit();
			}
		}
		else
		{ 
			// For inserting new advertisement
			$sql = "INSERT INTO cashbackengine_banners SET sort_order='$sort_order',link='$link',image='$loc', retailer_id='$retailer_id',bypass_script='$bypass_script'";

			if (smart_mysql_query($sql))
			{
				header("Location: banners.php?msg=added");
				exit();
			}
			
		}	
	}
	else
	{
		$errormsg = "";
		foreach ($errors as $errorname)
		{
			$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}
}
require_once ("inc/header.inc.php");

?>
<h2><?php echo $title;?></h2>

<?php if (isset($errormsg) && $errormsg != "") { ?>
	<div class="error_box"><?php echo $errormsg; ?></div>
<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
	<div class="success_box">Banner has been successfully added.</div>
<?php } ?>
	
	<form action="" method="post" enctype="multipart/form-data">
		<table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
			</tr>
			<tr>
            <td nowrap="nowrap" width="30%" valign="middle" align="right" class="tb1"><span class="req">* </span>Store:</td>
            <td width="70%" valign="top">
			<select class="textbox2" id="retailer_id" name="retailer_id">
			<option value="">--- Please select store ---</option>
				<?php

					$sql_retailers = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE status='active' ORDER BY title ASC");
				
					while ($row_retailers = mysql_fetch_array($sql_retailers))
					{
						if ($retailer_id == $row_retailers['retailer_id']) $selected = " selected=\"selected\""; else $selected = "";
						echo "<option value=\"".$row_retailers['retailer_id']."\"".$selected.">".$row_retailers['title']."</option>";
					}
				?>
			</select>
			</td>
			</tr>
			
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">*</span>Image:</td>
				<td align="left">
					<input type="file" name="image" id="image" />
					<?php if($id && $_POST['existingBannerImage']!=''){ ?>
						<a target="_blank" href="<?php echo $_POST['existingBannerImage']?>"><img style="width:50%" src="<?php echo $_POST['existingBannerImage']?>" /></a>
					<?php } ?>
				</td>
			</tr>

			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">*</span>Link:</td>
				<td align="left">
					<input type="text" name="link" id="link" value="<?php echo $link; ?>" size="40" class="textbox" />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1"><span class="req">*</span>Sort Order:</td>
				<td valign="middle"><input type="text" class="textbox" name="sort_order" value="<?php echo $sort_order; ?>" size="5" /></td>
            </tr>
             <tr>
				<td valign="middle" align="right" class="tb1">Bypass Retailer Script?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="bypass_script" value="1" <?php if (getPostParameter('bypass_script') == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td valign="middle" align="left">
					<input type="hidden" name="action" id="action" value="add" />
					<input type="submit" name="add" id="add" class="submit" value="<?php echo $title;?>" />
				</td>
			</tr>
		</table>
	</form>


<?php require_once ("inc/footer.inc.php"); ?>