<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

function fc_wp_connect(){
	$connection = mysql_connect(FC_WP_DB_HOST,FC_WP_DB_USER,FC_WP_DB_PASSWORD) or die ('Could not connect to MySQL server');;
	mysql_select_db(FC_WP_DB_NAME) or die ('Could not select database');
	
	return $connection;
}

function fc_wp_close($connection){
	$connection = mysql_close($connection);
}

function fc_get_users() 
{
	$conn = fc_wp_connect();
	$r = mysql_query('SELECT 
	user.ID,
	user.user_nicename,
	t1.meta_value AS "author_city", 
	t2.meta_value AS "author_state",
	t3.meta_value AS "Author_Profile_Picture" ,
    t4.meta_value AS "first_name",
    t5.meta_value AS "show_author_at_home"
    
	FROM wp_users AS user 
	LEFT JOIN wp_usermeta AS t1 
	ON user.ID=t1.user_id 
	LEFT JOIN wp_usermeta AS t2 
	ON t1.user_id=t2.user_id 
	LEFT JOIN wp_usermeta AS t3 
	ON t2.user_id = t3.user_id 
    LEFT JOIN wp_usermeta AS t4
    ON t3.user_id = t4.user_id
    INNER JOIN wp_usermeta AS t5
    ON t4.user_id = t5.user_id
	WHERE 
	t1.meta_key="author_city" && 
	t2.meta_key="author_state" && 
	t3.meta_key="author_profile_picture" &&
    t4.meta_key = "first_name" &&
    t5.meta_key ="show_author_at_home" &&
	t5.meta_value = 1    
    ', $conn);
	
	while($row=mysql_fetch_assoc($r))
	{
		$rows[]=$row;
	}
	fc_wp_close($conn);
	return $rows;
}