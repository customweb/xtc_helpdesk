<?php
/**
::Header::
 */

$smarty = new Smarty;

if(!isset($_GET['tId']))
	xtc_redirect(xtc_href_link(FILENAME_HELPDESK, '', 'SSL'));

# Check if is login:
$login = false;
if(isset($_GET['key']) && generateKeyForTicket($_GET['tId']) == $_GET['key'] )
{
	$login = true;
}
elseif(isset($_SESSION['customer_id']))
{
	$sql = '
		SELECT 
			*
		FROM
			' . TABLE_HELPDESK_TICKETS . ' AS t
		WHERE
			t.ticket_id = \'' . xtc_db_input($_GET['tId']) . '\'
		';
	$rs = xtc_db_query($sql);
	if($row = xtc_db_fetch_array($rs))
	{
		if($row['customers_id'] == $_SESSION['customer_id'])
			$login = true;
	}
}
	
if($login == false)
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));


# Add new answer to database:
if(isset($_POST['comment']) && $_POST['comment'] != HELPDESK_TEXT_ENTER_HERE_QUESTION)
{
	$sql = '
		SELECT 
			*,
			th.customers_id AS writer
		FROM
			' . TABLE_HELPDESK_TICKETS . ' AS t,
			' . TABLE_HELPDESK_TICKETS_HISTORY . ' AS th
		WHERE
			t.ticket_id = \'' . xtc_db_input($_GET['tId']) . '\'
		  AND
			t.ticket_id = th.ticket_id
		ORDER BY
			th.last_modified
		';
	$rs = xtc_db_query($sql);
	$row = xtc_db_fetch_array($rs);

	$sql = '
		INSERT INTO 
			' . TABLE_HELPDESK_TICKETS_HISTORY . '
		(
			customers_id,
			ticket_id,
			status_id,
			last_modified,
			department_id,
			priority_id,
			comment,
			add_by
		)
		VALUES
		(
			\'' . xtc_db_input($_POST['customers_id']) . '\',
			\'' . xtc_db_input($_GET['tId']) . '\',
			\'' . xtc_db_input(HELPDESK_TICKETS_DEFAULT_STATUS) . '\',
			NOW(),
			\'' . xtc_db_input($row['department_id']) . '\',
			\'' . xtc_db_input($row['priority_id']) . '\',
			\'' . xtc_db_input($_POST['comment']) . '\',
			\'customer\'
		)
		';
	xtc_db_query($sql);
	
	# Send E-Mails:
	$smarty->assign('ticket_no', $_GET['tId']);
	$smarty->assign('ticket_link', HTTPS_SERVER.DIR_WS_CATALOG.'helpdesk.php?action=view_ticket&key='.generateKeyForTicket($_GET['tId']).'&tId='.$_GET['tId']);
	
	
	// Select customer_id
	$sql = '
		SELECT 
			customers_id,
			customers_email,
			customers_firstname,
			customers_lastname
		FROM
			' . TABLE_HELPDESK_TICKETS . ' AS t
		WHERE
			t.ticket_id = \'' . xtc_db_input($_GET['tId']) . '\'
		';
	$rs = xtc_db_query($sql);
	$row = xtc_db_fetch_array($rs);

	if(isset($row['customers_id']))
	{
		$sql = 'SELECT * FROM '.TABLE_CUSTOMERS.' WHERE customers_id = \'' . xtc_db_input($row['customers_id']) . '\'';
		$rs = xtc_db_query($sql);
		$c = xtc_db_fetch_array($rs);
		$customerName = $c['customers_firstname'] . ' ' . $c['customers_lastname'];
		$customerEmailAddress = $c['customers_email_address'];
	}
	else
	{
		$customerName = $row['customers_firstname'] . ' ' . $row['customers_lastname'];
		$customerEmailAddress = $row['customers_email'];
	}
	
	
	// To staff:
	if(HELPDESK_TICKETS_EMAIL_STAFF_NEW_ANSWER == 'True')
	{
		// Text:
		$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/staff_new_answer.html');
		$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/staff_new_answer.txt');
		
		// Subject:
		$subject = str_replace('{$ticket_no}', $_GET['tId'], HELPDESK_TICKETS_EMAIL_STAFF_NEW_ANSWER_SUBJECT);
		
		// Get involved staff:
		$sql = '
				SELECT
					*
				FROM 
					' . TABLE_HELPDESK_STAFF . ' AS hs,
					' . TABLE_CUSTOMERS . ' AS c
				WHERE
					(
						hs.departments REGEXP \''.xtc_db_input($_POST['department_id']).',\'
					  OR
						hs.departments REGEXP \''.xtc_db_input($_POST['department_id']).'$\'
					  OR
						hs.departments = \'all\'
					)
				  AND
					(
						hs.languages REGEXP \''.(int)$_SESSION['languages_id'].',\'
					  OR
						hs.languages REGEXP \''.(int)$_SESSION['languages_id'].'$\'
					  OR
						hs.languages = \'all\'
					)
				  AND
					hs.customers_id = c.customers_id
				';
		$rs = xtc_db_query($sql);
		while($row = xtc_db_fetch_array($rs))
		{
			// Send mail:
			xtc_php_mail(
					$customerEmailAddress,
					$customerName,
					$row['customers_email_address'],
					$row['customers_firstname'] . ' ' . $row['customers_lastname'],
					'',
					$customerEmailAddress,
					$customerName,
					'',
					'',
					$subject,
					$html_mail,
					$txt_mail
				);
		}
	}
	$smarty->assign('TICKET_MSG', HELPDESK_TEXT_NEW_ANSWER_ADDED );

}

# include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

# Navbar:
$breadcrumb->add(NAVBAR_HELPDESK, xtc_href_link(FILENAME_HELPDESK, '', 'SSL'));
$breadcrumb->add(sprintf(NAVBAR_HELPDESK_TICKET_VIEW, $_GET['tId']), xtc_href_link(FILENAME_HELPDESK, xtc_get_all_get_params(), 'SSL'));

require (DIR_WS_INCLUDES.'header.php');

# Add variables:
// Ticket:
$sql = '
		SELECT 
			*,
			t.customers_id AS customers_id,
			th.customers_id AS writer
		FROM
			' . TABLE_HELPDESK_TICKETS . ' AS t,
			' . TABLE_HELPDESK_DEPARTMENTS . ' AS d,
			' . TABLE_HELPDESK_TICKETS_STATUS . ' AS s,
			' . TABLE_HELPDESK_TICKETS_PRIORITY . ' AS p,
			' . TABLE_HELPDESK_TICKETS_HISTORY . ' AS th
		WHERE
			t.ticket_id = \'' . xtc_db_input($_GET['tId']) . '\'
		  AND
			t.ticket_id = th.ticket_id
		  AND
		  	th.internal != 1
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
		';
$rs = xtc_db_query($sql);
$ticket = array();
while($row = xtc_db_fetch_array($rs))
{
	$row['date'] = xtc_date_long($row['last_modified']); //  . date(' - G:i', strtotime($row['last_modified']))
	if(empty($row['writer']) or $row['writer'] == 0)
	{
		$row['title'] = sprintf(HELPDESK_TEXT_VIEW_TICKET, $row['customers_firstname'] . ' ' . $row['customers_lastname']);
	}
	else
	{
		$sql = 'SELECT * FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = \'' . xtc_db_input($row['writer']) . '\'';
		$r = xtc_db_query($sql);
		$writer = xtc_db_fetch_array($r);
		$row['title'] = sprintf(HELPDESK_TEXT_VIEW_TICKET, $writer['customers_firstname'] . ' ' . $writer['customers_lastname']);
	}
	$row['comment'] = nl2br( $row['comment'] );
	$ticket[ $row['history_id'] ] = $row;
	$last = $row;
}
$smarty->assign('HISTORY', $ticket);


# Add Msg:
if($_GET['new'] == '1')
{
	$messageStack->add('view_ticket', HELPDESK_TEXT_NEW_TICKET_ADDED, 'success');
}

# Add title:
$smarty->assign('TICKET_TITLE', sprintf(HELPDESK_TEXT_VIEW_TICKET_TITLE, $_GET['tId'] . ' - ' . $last['status_name']) );

# Add Status:
$smarty->assign('TICKET_STATUS', $last['status_name'] );

# Add Priority:
$smarty->assign('TICKET_PRIORITY', $last['priority_name'] );

# Add Priority:
$smarty->assign('TICKET_DEPARTMENT', $last['department_name'] );

# Add Priority:
$smarty->assign('TICKET_ID', '#'.$_GET['tId'] );

# Add order link:
if($last['orders_id'] > 0)
	$smarty->assign('LINK_ORDER', '<a href="'.xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id='.$last['orders_id']).'">'.$last['orders_id'].'</a>' );

# Add form:
$smarty->assign('FORM_HEAD', xtc_draw_form('add_answer', xtc_href_link(FILENAME_HELPDESK, xtc_get_all_get_params(array('new')), 'SSL'), 'post'));
$smarty->assign('FORM_FOOTER', '</form>');


# Add history textfield:
$txtHiddenField = xtc_draw_hidden_field('customers_id', $last['customers_id']);
$txtArea = '<textarea style="width:100%;" rows="16" name="comment" 
			onfocus="if(this.innerHTML == \''.HELPDESK_TEXT_ENTER_HERE_QUESTION.'\') this.innerHTML = \'\';" onblur="if(this.innerHTML ==\'\') this.innerHTML = \''.HELPDESK_TEXT_ENTER_HERE_QUESTION.'\';">'.HELPDESK_TEXT_ENTER_HERE_QUESTION.'</textarea>';

$smarty->assign('COMMENT_FIELD', $txtHiddenField.$txtArea);

# Add some lang vars:
$smarty->assign('TEXT_LINK_ORDER', HELPDESK_TEXT_VIEW_TICKET_ORDER_LINK );
$smarty->assign('TEXT_TICKET_STATUS', HELPDESK_TEXT_STATUS );
$smarty->assign('TEXT_TICKET_DEPARTMENT', HELPDESK_TEXT_DEPARTMENT );
$smarty->assign('TEXT_TICKET_PRIORITY', HELPDESK_TEXT_PRIORITY );
$smarty->assign('TEXT_TICKET_ID', HELPDESK_TEXT_TICKET_ID );
$smarty->assign('TEXT_COMMENT_TITLE', HELPDESK_TEXT_VIEW_TICKET_TITLE_ANSWER);


# Add Save Button:
$smarty->assign('SEND_BUTTON', xtc_image_submit('button_send.gif', IMAGE_BUTTON_SEND) );

# Message Stack
if ($messageStack->size('view_ticket') > 0)
{
	$smarty->assign('MESSAGE', $messageStack->output('view_ticket'));
}


# Generate main Content:
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/helpdesk/view_ticket.html');

# Generate index:
$smarty->assign('main_content', $main_content);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;

# Output html:
$smarty->display(CURRENT_TEMPLATE.'/index.html');
?>