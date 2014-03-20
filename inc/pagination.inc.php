<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/


/**
 * Creates pagination
 * @param	$table		table name
 * @param	$limit		result's limit
 * @param	$target		link's target
 * @param	$where		additional WHERE parameter
 * @return	string		returns pagination
*/

function ShowPagination($table, $limit, $target, $where = "")
{
	$main_sql = "SELECT COUNT(*) AS total FROM cashbackengine_".$table." ".$where;
	$total_pages = mysql_fetch_array(smart_mysql_query($main_sql));
	$total_pages = $total_pages['total'];
	$adjacents = "3";
	$page = $_GET['page'];
	
	if ($page)
		$start = ($page - 1) * $limit;
	else
		$start = 0;
	 
	if ($page == 0) $page = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($total_pages/$limit);
	$lpm1 = $lastpage - 1;

	$pagination = "";

	if($lastpage > 1)
	{  
		$pagination .= "<div class='pagination'>";
		
		if ($page > 1)
			$pagination.= "<a href='".$target."page=$prev'>&#139; Previous</a>";
		else
			$pagination.= "<span class='disabled'>&#139; Previous</span>";  
	 
		if ($lastpage < 7 + ($adjacents * 2))
		{  
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				$pagination.= "<span class='curPage'>$counter</span>";
				else
				$pagination.= "<a href='".$target."page=$counter'>$counter</a>";                  
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))
		{
			if($page < 1 + ($adjacents * 2))      
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class='curPage'>$counter</span>";
					else
						$pagination.= "<a href='".$target."page=$counter'>$counter</a>";                  
				}
				$pagination.= "...";
				$pagination.= "<a href='".$target."page=$lpm1'>$lpm1</a>";
				$pagination.= "<a href='".$target."page=$lastpage'>$lastpage</a>";      
			}
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href='".$target."page=1'>1</a>";
				$pagination.= "<a href='".$target."page=2'>2</a>";
				$pagination.= "...";
				
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class='curPage'>$counter</span>";
					else
						$pagination.= "<a href='".$target."page=$counter'>$counter</a>";                  
				}
				
				$pagination.= "..";
				$pagination.= "<a href='".$target."page=$lpm1'>$lpm1</a>";
				$pagination.= "<a href='".$target."page=$lastpage'>$lastpage</a>";      
			}
			else
			{
				$pagination.= "<a href='".$target."page=1'>1</a>";
				$pagination.= "<a href='".$target."page=2'>2</a>";
				$pagination.= "..";
				
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class='curPage'>$counter</span>";
					else
						$pagination.= "<a href='".$target."page=$counter'>$counter</a>";                  
				}
			}
	}
	 
	if ($page < $counter - 1)
		$pagination.= "<a href='".$target."page=$next'>Next &#155;</a>";
	else
		$pagination.= "<span class='disabled'>Next &#155;</span>";
		$pagination.= "</div>\n";      
	}

		return $pagination;
	}

?>