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
	$title = "Edit Advertisement";
}
else 
{
	$title = "Add Advertisement";
}

//first time page load
if(!$isPost){
	$select_option = 'use_url';	
}

//Run in case of edit to fetch advertisement information
if($id && !$isPost)
{
	$fetch_record 	=	mysql_fetch_assoc(smart_mysql_query("SELECT a.title,a.advertisement_id, a.image_url, link, a.image_name, r.retailer_id FROM cashbackengine_advertisements as a 
	left join cashbackengine_retailers as r on r.retailer_id = a.retailer_id
	WHERE advertisement_id=$id"));
	$image_url		=	$fetch_record['image_url'];
	$link			=	$fetch_record['link'];
	$image_name		=	$fetch_record['image_name'];
	$retailer_id	= 	$fetch_record['retailer_id'];
	
	$select_option = $image_url!='' ? 'use_url' : 'upload_image';
	$_POST = $fetch_record;
	
	$_POST["existingAdImage"] = $image_url!='' ? $image_url : $image_name;
	
	if(empty($fetch_record)){
		header("Location: advertisements.php?msg=invalid_ad&msg_type=error");
		exit();
	}
}

if ($isPost)
{
	unset($errors);
	$errors = array();
	$select_option	=	$_POST['radiourl'];
	
	$image_url		= mysql_real_escape_string(getPostParameter('imageurl'));
	$link			= mysql_real_escape_string(getPostParameter('link'));
	$adTitle		= mysql_real_escape_string(getPostParameter('title'));
	$image_name		= $_FILES['image']['name'];
	$retailer_id	= mysql_real_escape_string(getPostParameter('retailer_id'));

	if($isEdit){
		//if ($link=='' || $adTitle=='' || ($select_option=='use_url' && $image_url=='') || $retailer_id=='')
		if ($adTitle=='' || ($select_option=='use_url' && $image_url=='') || $retailer_id=='')
		{
			$errors[] = "Please ensure that all fields marked with an asterisk are complete";
		}	
	}//elseif(!$isEdit && ($link=='' || ($image_url=='' && $image_name=='') ||  $adTitle=='' || $retailer_id=='')){
	elseif(!$isEdit && (($image_url=='' && $image_name=='') ||  $adTitle=='' || $retailer_id=='')){
		$errors[] = "Please ensure that all fields marked with an asterisk are complete";
	}
	
	
	
	if(!$errors)
	{	
		//validate advertisement image
		if($select_option=='upload_image' && $image_name!=''){
			if ($_FILES["image"]["error"] > 0)
	  		{
				$error[]= $_FILES["image"]["error"];
			}else{
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
			  		$errors[] = 'Uplaod valid image.';
				}
				
				if($_FILES["image"]["size"] > 5242880){
					$errors[] = 'Allowed maximum file size is 5MB.';
				}
			}
			 
		}elseif($select_option=='use_url'){		//validate advertisement image url
			if (!preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$image_url))
			{
				$errors[] = "Enter correct image url. (e.g. 'http://abc.com' or 'https://abc.com' or 'http://www.abc.com')";
			}			
		}
		
		//validate advertisement link
		
		if (isset($link) && $link!="" && !preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $link))
		{
			$errors[] = "Enter correct link url. (e.g. 'http://abc.com' or 'https://abc.com' or 'http://www.abc.com')";
		}
	}
	
	if (count($errors) == 0 && $isPost)
	{
		if($select_option=='upload_image' && $image_name!='')
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
		}
		
		//Update record
		if($id)
		{	
			if($image_url!='' || ($select_option=='upload_image' && $image_name!='')){
				//remove image if one already there for advertisement		
				$sql=mysql_query("SELECT image_name FROM cashbackengine_advertisements WHERE advertisement_id='$id'");
				$row=mysql_fetch_assoc($sql);
				$image_loc = $row['image_name'];
			
				if($image_loc!="" && file_exists($image_loc))	
					$img_del_result=unlink($image_loc);
			}	
			//remove block ends		
			
			$params = array(
				'title'=>$adTitle,
				'link' => $link,
				'retailer_id' => $retailer_id
			);
			
			
			if($select_option=='upload_image'){
				$additionalParams = array(
					'image_url' => ''
				);
				if($image_name!='')
					$additionalParams['image_name']= $loc;

			}else{
				$additionalParams = array(
					'image_url' => $image_url,
					'image_name' => ''
				);
			}

			$params = array_merge($params, $additionalParams);
			$set = array();
			
			foreach($params as $pkey => $pvalue){
				$set[] = $pkey."='".$pvalue."'";	
			}
			$set = implode(',', $set);
			
			echo $sql = "UPDATE cashbackengine_advertisements  SET $set WHERE advertisement_id='$id'";
			
			if(smart_mysql_query($sql))
			{
				header("Location: advertisements.php?msg=updated");
				exit();
			}
		}
		else
		{ 
			// For inserting new advertisement
			if($select_option=="upload_image")
			{
				$sql = "INSERT INTO cashbackengine_advertisements SET retailer_id='$retailer_id',title='$adTitle', link='$link',image_name='$loc'";
			}
			else
			{
				$sql = "INSERT INTO cashbackengine_advertisements SET retailer_id='$retailer_id',title='$adTitle', image_url='$image_url',link='$link'"; 
			}

			if (smart_mysql_query($sql))
			{
				header("Location: advertisements.php?msg=added");
				exit();
			}
			
			header("Location: advertisements.php?msg=exists");
			exit();
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
	<div class="success_box">Advertisement has been successfully added.</div>
<?php } ?>
	
	<form action="" method="post" enctype="multipart/form-data">
		<table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
			</tr>
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Store :</td>
				<td align="left">
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
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Title :</td>
				<td align="left">
					<input type="text" name="title" id="title" value="<?php echo getPostParameter('title'); ?>" size="40" class="textbox" />
				</td>
			</tr>
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Advertisement Image:</td>
				<td align="left">
					<input type="radio" name="radiourl" class="radiourl" id="use_url" value="use_url" <?php if($select_option=='use_url') {?> checked <?php	}?>>Image URL&nbsp;
					<input type="radio" name="radiourl" class="radiourl" id="upload_image" value="upload_image" <?php if($select_option=='upload_image'){?> checked <?php }?>>Upload Image
				</td>
			</tr>
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req"> </span></td>
				<td align="left">
					<input type="text" name="imageurl" id="imageurl" value="<?php echo $image_url; ?>" size="40" class="textbox" />
					<input type="file" name="image" id="image" />
					<?php if($id && $_POST['existingAdImage']!=''){ ?>
						<input type="hidden" name="existingAdImage" value="<?php echo getPostParameter('existingAdImage')?>"/>
						<a target="_blank" href="<?php echo $_POST['existingAdImage']?>"><img style="width:50%" src="<?php echo $_POST['existingAdImage']?>" /></a>
					<?php } ?>
				</td>
			</tr>
	
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1">Link :</td>
				<td align="left">
					<input type="text" name="link" id="link" value="<?php echo $link; ?>" size="40" class="textbox" />
				</td>
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

<script>
$(function(){
	$("#image").hide();
	$("#imageurl").hide();
	
	var radioImageChecked = $('#upload_image').is(':checked');
	var radioUrlChecked = $('#use_url').is(':checked');
	
	if(radioImageChecked){
		$("#image").show();
		$("#imageurl").hide();	
	}else if(radioUrlChecked){
		$("#imageurl").show();
		$("#image").hide();
	}
		
	$('.radiourl').click(function()	{
		var value = $(this).val();
		if(value=="use_url"){
			$("#imageurl").show();
			$("#image").hide();
		}
		else if(value="upload_image"){
			$("#image").show();
			$("#imageurl").hide();
		}
	});
});
</script>
<?php require_once ("inc/footer.inc.php"); ?>