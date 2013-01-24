<?php
/**
::Header::
 */
 
# Check if is login:
if(!isset ($_SESSION['customer_id']))
    xtc_redirect(xtc_href_link(FILENAME_HELPDESK, 'action=new_ticket', 'SSL'));

$smarty = new Smarty;
# include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

# Navbar:
$breadcrumb->add(NAVBAR_HELPDESK, xtc_href_link(FILENAME_HELPDESK, '', 'SSL'));

require (DIR_WS_INCLUDES.'header.php');

# Add variables:
// Tickets:
if(isset ($_SESSION['customer_id']))
{
    $sql = '
            SELECT 
                *
            FROM
                ' . TABLE_HELPDESK_TICKETS . ' AS t
            WHERE
                t.customers_id = \'' . xtc_db_input($_SESSION['customer_id']) . '\'
			ORDER BY
				ticket_date DESC
            ';
    $rs = xtc_db_query($sql);
    $tickets = array();
    while($row = xtc_db_fetch_array($rs))
    {
        $sql = '
			SELECT
				* 
			FROM 
				' . TABLE_HELPDESK_TICKETS_HISTORY . ' AS th, 
				' . TABLE_HELPDESK_DEPARTMENTS . ' AS d,
				' . TABLE_HELPDESK_TICKETS_STATUS . ' AS s,
				' . TABLE_HELPDESK_TICKETS_PRIORITY . ' AS p
			WHERE 
				ticket_id = ' . $row['ticket_id'] . ' 
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
		$row['link'] = '<a href="' . xtc_href_link(FILENAME_HELPDESK, xtc_get_all_get_params('tId', 'action').'action=view_ticket&tId='.$row['ticket_id'], 'SSL') . '">' . TEXT_VIEW . '</a>';
		$tickets[ $row['ticket_id'] ] = $row;
    }
    $smarty->assign('TICKETS', $tickets);
}

# Add some lang vars:
$smarty->assign('HEADING_TICKET_NO', HELPDESK_OVERVIEW_HEADING_TICKET_NO);
$smarty->assign('HEADING_TICKET_DATE', HELPDESK_OVERVIEW_HEADING_TICKET_DATE);
$smarty->assign('HEADING_TICKET_STATUS', HELPDESK_OVERVIEW_HEADING_TICKET_STATUS);
$smarty->assign('HEADING_TICKET_VIEW', HELPDESK_OVERVIEW_HEADING_TICKET_VIEW);
$smarty->assign('OVERVIEW_TITLE', NAVBAR_HELPDESK);
$smarty->assign('OVERVIEW_NO_TICKETS', HELPDESK_TEXT_ERROR_NO_TICKETS);

#Link new ticket:
$smarty->assign('LINK_NEW_TICKET', '<a href="'. xtc_href_link(FILENAME_HELPDESK, 'action=new_ticket', 'SSL').'">' . NAVBAR_HELPDESK_NEW_TICKET . '</a>');

# Generate main Content:
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/helpdesk/overview.html');

# Generate index:
$smarty->assign('main_content', $main_content);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;

# Output html:
$smarty->display(CURRENT_TEMPLATE.'/index.html');
?>