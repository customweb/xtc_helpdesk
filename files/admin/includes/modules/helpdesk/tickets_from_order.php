<?php
/**
::Header::
 */

define('FILENAME_MY_HELPDESK', 'my_helpdesk.php');
define('FILENAME_HELPDESK', 'helpdesk.php');
define('TABLE_HELPDESK_DEPARTMENTS', 'helpdesk_departments');
define('TABLE_HELPDESK_TICKETS', 'helpdesk_tickets');
define('TABLE_HELPDESK_TICKETS_HISTORY', 'helpdesk_tickets_history');
define('TABLE_HELPDESK_TICKETS_PRIORITY', 'helpdesk_tickets_priority');
define('TABLE_HELPDESK_TICKETS_STATUS', 'helpdesk_tickets_status');
define('TABLE_HELPDESK_STAFF', 'helpdesk_staff');

$sql = '
		SELECT 
			t.ticket_id AS ticket_id,
			t.customers_id AS ticket_customers_id,
			t.customers_firstname,
			t.customers_lastname,
			t.customers_email,
			t.ticket_date
		FROM
			' . TABLE_HELPDESK_TICKETS . ' AS t, 
			' . TABLE_HELPDESK_TICKETS_HISTORY . ' AS th
		WHERE
			t.ticket_id = th.ticket_id
		  AND
			t.orders_id = \'' . $_GET['oID'] . '\'
		GROUP BY
			t.ticket_id
		ORDER BY
			th.last_modified DESC
		';
$rs = xtc_db_query($sql);
$tickets = array();

$i = 0;
while($row = xtc_db_fetch_array($rs))
{
	$sql = '
		SELECT
			*,
			th.customers_id AS history_customers_id,
			p.sort_order AS priority_sort,
			s.sort_order AS status_sort
		FROM 
			' . TABLE_HELPDESK_TICKETS_HISTORY . ' AS th, 
			' . TABLE_HELPDESK_DEPARTMENTS . ' AS d,
			' . TABLE_HELPDESK_TICKETS_STATUS . ' AS s,
			' . TABLE_HELPDESK_TICKETS_PRIORITY . ' AS p
		WHERE 
			th.ticket_id = ' . $row['ticket_id'] . ' 
		  AND
			p.priority_id = th.priority_id
		  AND
			p.languages_id = \'' . (int) $_SESSION['languages_id'] . '\'
		  AND
			s.status_id = th.status_id
		  AND
			s.languages_id = \'' . (int) $_SESSION['languages_id'] . '\'
		  AND
			d.department_id = th.department_id
		  AND
			d.languages_id = \'' . (int) $_SESSION['languages_id'] . '\'
		ORDER BY 
			last_modified DESC';
	$r = xtc_db_query($sql);
	$history = xtc_db_fetch_array($r);
	
	$row = array_merge($history, $row);
	$tickets[$i] = $row;
	$i++;
}

if(count($tickets) > 0)
{
	echo '<table class="main" style="width: 100%; max-width: 500px;">';
	
	echo '<tr>';
		echo '<td><b>#</b></td>';
		echo '<td><b>Datum:</b></td>';
		echo '<td><b>Status:</b></td>';
		echo '<td><b>'.DISPLAY_MEMOS . '</b></td>';
	echo '</tr>';
	
	foreach($tickets as $data)
	{
		echo '<tr>';
			echo '<td>';
				echo $data['ticket_id'];
			echo '</td>';
			echo '<td>';
				echo $data['last_modified'];
			echo '</td>';
			echo '<td>';
				echo $data['status_name'];
			echo '</td>';
			echo '<td>';
				echo '<a href="helpdesk.php?tId='.$data['ticket_id'].'&page=edit_ticket">'.DISPLAY_MEMOS.'</a>';
			echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

?>