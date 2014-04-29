<?php

// Database Connection
require_once("../inc/config.inc.php");

if(isset($_POST['file_name']) && $_POST['file_name']!="")
{
	// Fetch Record from Database
	if($_POST['file_name'] == 'payments')
	{
		$query = "SELECT
					u.username,
					t.reference_id,
					t.payment_type,
					t.amount, 
					DATE_FORMAT(t.created, '%e %b %Y') AS 'created date',
					DATE_FORMAT(t.transaction_date, '%e %b %Y') AS 'transaction date', 
					t.status,
					u.newsletter AS 'email_subsciption'
					FROM cashbackengine_transactions t, 
					cashbackengine_users u WHERE t.user_id=u.user_id";
	}elseif($_POST['file_name'] == 'users')
	{
		$query = "SELECT 
					u.fname AS 'first name',
					u.lname AS 'last name',
					u.email,
					u.address as 'address1',
					u.address2,
					u.city,
					u.state,
					u.zip,
					c.name AS country,
					SUM(t.amount) AS balance 
					FROM cashbackengine_users AS u LEFT JOIN cashbackengine_transactions AS t 
					ON u.user_id = t.user_id LEFT JOIN cashbackengine_countries AS c 
					ON u.country = c.country_id GROUP BY u.user_id";
	}
	$output = "";
 	$file_name = 'default';
	$sql = mysql_query("$query");
	$columns_total = mysql_num_fields($sql);
	
	// Get The Field Name
	
	for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysql_field_name($sql, $i);
		$output .= '"'.$heading.'",';
	}
	$output .="\n";
	
	// Get Records from the table
	
	while ($row = mysql_fetch_array($sql)) {
		for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
		}
		$output .="\n";
	}
	
	// Download the file
	
	$filename = $file_name.'.csv';
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename='.$filename);
	echo $output;
	exit;		
}
?>