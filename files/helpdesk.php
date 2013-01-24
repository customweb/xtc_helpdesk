<?php
/**
::Header::
 */
 

// add some defines:
define('FILENAME_HELPDESK', 'helpdesk.php');
define('TABLE_HELPDESK_DEPARTMENTS', 'helpdesk_departments');
define('TABLE_HELPDESK_TICKETS', 'helpdesk_tickets');
define('TABLE_HELPDESK_TICKETS_HISTORY', 'helpdesk_tickets_history');
define('TABLE_HELPDESK_TICKETS_PRIORITY', 'helpdesk_tickets_priority');
define('TABLE_HELPDESK_TICKETS_STATUS', 'helpdesk_tickets_status');
define('TABLE_HELPDESK_STAFF', 'helpdesk_staff');


include ('includes/application_top.php');
require_once (DIR_FS_INC.'xtc_date_short.inc.php');
require_once (DIR_FS_INC.'xtc_date_long.inc.php');
require_once (DIR_FS_INC.'xtc_validate_email.inc.php');

function generateKeyForTicket($ticketId)
{
	$sql = 'SELECT * FROM ' . TABLE_HELPDESK_TICKETS . ' WHERE ticket_id = \'' . xtc_db_input($ticketId) . '\'';
	$rs = xtc_db_query($sql);
	if($row = xtc_db_fetch_array($rs))
	{
		return md5($row['ticket_id'].$row['customers_id'].$row['customers_fistname'].$row['customers_lastname'].$row['customers_email'].$row['ticket_date']);
	}
	else
	{
		return false;
	}
	
}

switch($_GET['action'])
{
	# show a form for new ticket
	case 'new_ticket':		
		require DIR_WS_MODULES.'helpdesk/new_ticket.php';
		break;

	# show a ticket
	case 'view_ticket':
		require DIR_WS_MODULES.'helpdesk/view_ticket.php';
		break;
	
	# show default screen:
	default:
		require DIR_WS_MODULES.'helpdesk/overview.php';
		break;
}


include ('includes/application_bottom.php');

?>