<?php
/*
Plugin Name: Easy Author Image
Plugin URI: http://lawsonry.com/
Description: Adds an author image uploader to your profile page. Upload an author image right from your profile page with the click of a button.
Version: 1.5
Author: Jesse Lawson
Author URI: http://www.lawsonry.com
License: GPL2

Hey I love learning from other people's code too, so please feel free to dive into this and if you don't understand anything or if you're confused about something, go to the plugin page and leave me a comment. 

Same goes for if you have any suggestions or comments. 

Thanks!

- Jesse Lawson
- Lawsonry.com (my wordpress blog)

*/

// Enqueue scripts for back-end use
function q_enqueue_backend_scripts() {
	
	// Register our .js that triggers a custom media upload box to show up when we are uploading our author profile image
	wp_register_script( 'easy-author-image-uploader', plugins_url('js/easy-author-image-uploader.js', __FILE__), array('jquery','media-upload','thickbox') );

	// If we are currently viewing the profile field, enqueue our custom js file
	if ( 'profile' == get_current_screen() -> id || 'user-edit' == get_current_screen() ->id ) {
		wp_enqueue_script('jquery');

		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');

		wp_enqueue_script('media-upload');
		wp_enqueue_script('easy-author-image-uploader');

	}

}
add_action('admin_enqueue_scripts', 'q_enqueue_backend_scripts');



function easy_author_image_init() {
	global $pagenow;
	if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
		add_filter( 'gettext', 'q_replace_thickbox_button_text', 1, 3 ); // here we call our func to replace the button text for the avatar uploader
	}

}	

// Initialize the options
add_action('admin_init', 'easy_author_image_init'); 


// Here we grab the css for the elements in our admin page
function q_plugin_styles() {
	wp_register_style('easy_author_image', plugins_url('css/easy-author-image.css', __FILE__));
	wp_enqueue_style('easy_author_image');
}

add_action('wp_enqueue_scripts', 'q_plugin_styles');

// First, we'll add a special filter to change the text of the image uploader text when we're uploading an avatar	
function q_replace_thickbox_button_text($translated_text, $text, $domain) {
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'profile' );
			if ( $referer != '' ) {
				return __('Make this my author profile picture!', 'q' );
			}
		}
		return $translated_text;
	}
	
// Second, we'll have to manually push the new new profile field onto the profile page (as of 16-June-2013, user-edit.php (Core WP page) manually places the profile fields, and doesn't use do_settings_section(
function q_add_custom_profile_fields( $user ) {
	
	// Display image uploader button
	$avatar = get_the_author_meta( 'author_profile_picture', $user->ID );
	?>
		<h3>Profile Picture</h3>
		
		<input type="hidden" id="author_profile_picture_url" name="author_profile_picture_url" value="<?php echo esc_url( $avatar ); ?>" />

		<table class="form-table">
			<tr>
				<th><label for="author_profile_picture_button"><span class="description"><?php _e('Upload a picture to use as the profile image.', 'q' ); ?></span></label></th>
				<?php $buttontext = ""; if('' != $avatar) { $buttontext = "Change profile picture";  } else { $buttontext = "Upload new profile picture"; }?>
				<td>
					<input id="author_profile_picture_button" type="button" class="button" value="<?php echo $buttontext; ?>" />
					<?php if('' != $avatar){?><input id="author_profile_picture_remove" type="button" class="button" value="Delete profile picture" /><?php }?>
				</td>
			</tr>
			
			<tr>
				<th><label for="author_profile_picture_preview"><span class="description"><?php _e('Preview:', 'q' ); ?></span></label></th>
				<td>
					<?php if ( '' != $avatar ){ ?>
					<div id="author_profile_picture_preview" style="min-height: 100px;">
						<img style="max-width:100%;border:2px solid #CCC" src="<?php echo esc_url( $avatar ); ?>" />
					</div>					
					<span class="description">You can update the picture from above.</span>
				<?php } else { ?>
					<div id="author_profile_picture_preview" style="height: 100px; width:100px; line-height:100px; border:2px solid #CCC; text-align:center; font-size:5em;">?</div>					
					<span class="description">This profile does not yet have an image. Click the button above to upload one (or select one from your media gallery).</span>
				<?php } ?>
					<span id="upload_success" style="color: #FF0000; font-weight: bold; display:block;"></span>
				</td>
			</tr>
		</table>
	<?php
	
}

// Third, we'll create this callback function to be called when the profile field is saved. 
function q_save_custom_profile_fields( $user_id ) {
    
    if ( !current_user_can( 'edit_user', $user_id ) )
        return FALSE;
            
    update_user_meta( $user_id, 'author_profile_picture', $_POST['author_profile_picture_url'] );
}

// Add our functions to profile display and update hooks
add_action( 'show_user_profile', 'q_add_custom_profile_fields' );
add_action( 'edit_user_profile', 'q_add_custom_profile_fields' );
add_action( 'personal_options_update', 'q_save_custom_profile_fields' );
add_action( 'edit_user_profile_update', 'q_save_custom_profile_fields' );

// Now, let's create an easy function to grab our author image

function author_image_circle($user_id=999999, $_size="small") {
	
	if($user_id==999999){
		$avatar = get_the_author_meta('author_profile_picture', get_the_ID());
	} else {
		$avatar = get_the_author_meta('author_profile_picture', $user_id);
	}
	$size = ( ($_size == "small" || $_size == "medium" || $_size == "large") ? $_size : "medium");
	
	
	$output = '<div class="circular-'.$size.'" style="background: url('.$avatar.');"></div>';
	
	echo $output;
}

function get_author_image_url($user_id=999999) {
	if($user_id==999999){
		$avatar = get_the_author_meta('author_profile_picture', get_the_ID());
	} else {
		$avatar = get_the_author_meta('author_profile_picture', $user_id);
	}
	
	return $avatar;
}

// Add option Use your avatar as the default avatar
/*
 if ( !function_exists('addgravatar') ) {
   function addgravatar( $avatar_defaults ) {
   	$user = wp_get_current_user(); 
    $myavatar = get_the_author_meta( 'author_profile_picture', $user->ID );

     // Change path to your custom avatar
     $avatar_defaults[$myavatar] = 'My Easy Author Image Profile Picture'; 

     // Change to your avatar name
        return $avatar_defaults;
   }
   add_filter( 'avatar_defaults', 'addgravatar' );
 }
*/ 

// The meat and potatoes
	

function get_easy_author_image($avatar, $id_or_email, $size, $default='', $alt='') {
        // if this plugin is activated, we'll assume the author wants to use their Easy Author Image instead of Gravatar.
        // This will replace it.
        // FUTURE RELEASE: ALLOW USER TO SET MAX WIDTH HEIGHT VIA DISCUSSION SCREEN
       
        $myavatar = "";
       
        if ( is_numeric($id_or_email) ) {
                $id = (int) $id_or_email;
                $user = get_userdata($id);
                if ( $user )
                        $email = $user->user_email;
        } elseif ( is_object($id_or_email) ) {
 
                if ( !empty($id_or_email->user_id) ) {
                        $id = (int) $id_or_email->user_id;
                        $user = get_userdata($id);
                        if ( $user)
                                $email = $user->user_email;
                } elseif ( !empty($id_or_email->comment_author_email) ) {
                        $email = $id_or_email->comment_author_email;
                }
        } else {
                $email = $id_or_email;
        }
       
        // First see if they're a registered user with email set
        if(!empty($email)) {
                // user exists + has email
                $avatar_user = get_user_by('email', $email);
                // check if author_profile_picture is set
                $url = get_the_author_meta('author_profile_picture', $avatar_user->ID);
               
                if($avatar_user && $url){
                       
                        // there is a url so user has an author profile picture
                                $myavatar = '<img class="avatar avatar-'.$size.' photo" width="64" height="64" src="'.$url.'"/>';              
               
                } else {
                        // No author_profile_picture set OR user does not belong to blog, so default to Gravatar
                        $gravatarUrl = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5($email) . "&size=40";
                        $myavatar = "<img src='$gravatarUrl' height='64' width='64' alt=".$email."' />";
                }
        }else{
                $myavatar = "<img alt='' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
        }
                       
        return $myavatar;
}



add_filter( 'get_avatar', 'get_easy_author_image', 10, 5);

?>