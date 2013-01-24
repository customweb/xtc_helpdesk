<?php
/**
::Header::
 */

require_once (DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

// check if the user is a stuf:
$sql = 'SELECT * FROM ' . TABLE_HELPDESK_STAFF . ' WHERE customers_id = \'' . (int) $_SESSION['customer_id'] . '\'';
$rs = xtc_db_query($sql);
if(($staff = xtc_db_fetch_array($rs)) === false && $filename != 'helpdesk.php')
{
	xtc_redirect(xtc_href_link(FILENAME_HELPDESK, ''));
}


if(isset($_POST['save']) && !isset($_POST['search']))
{
	$sql = '
		SELECT 
			*,
			th.customers_id AS writer,
			t.customers_id AS customer
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
			th.last_modified DESC
		';
	$rs = xtc_db_query($sql);
	$row = xtc_db_fetch_array($rs);
	
	$exHistorySql = false;
	$exSql = false;
	$auto_comment = '';
	if($_POST['orders_id'] != '' && $row['orders_id'] == 0)
	{
		$orders_id = $_POST['orders_id'];
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_ORDERS_ID_ADDED, $_POST['orders_id']);
		$exSql = true;
	}
	elseif($_POST['orders_id'] != '' && $row['orders_id'] != $_POST['orders_id'])
	{
		$orders_id = $_POST['orders_id'];
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_ORDERS_ID_CHANGED, $row['orders_id'], $_POST['orders_id']);
		$exSql = true;
	}
	elseif($_POST['orders_id'] == '' && $row['orders_id'] != 0)
	{
		$orders_id = 0;
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_ORDERS_ID_DELETED, $row['orders_id']);
		$exSql = true;
	}
	else
	{
		$orders_id = $row['orders_id'];
	}
	
	if($_POST['customers_id'] != '' && ($row['customer'] == 0))
	{
		$customers_id = $_POST['customers_id'];
		$sql = 'SELECT customers_firstname, customers_lastname FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = \''. xtc_db_input($_POST['customers_id']) . '\' ';
		$r = xtc_db_query($sql);
		$customer = xtc_db_fetch_array($r);
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_CUSTOMER_ID_ADDED, $customer['customers_lastname'].', '.$customer['customers_firstname']);
		$exSql = true;
	}
	elseif($_POST['customers_id'] != '' && $row['customer'] != $_POST['customers_id'])
	{
		$customers_id = $_POST['customers_id'];
		$sql = 'SELECT customers_firstname, customers_lastname FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = \''. xtc_db_input($row['customer']) . '\' ';
		$r = xtc_db_query($sql);
		$customer = xtc_db_fetch_array($r);
		
		$sql = 'SELECT customers_firstname, customers_lastname FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = \''. xtc_db_input($_POST['customers_id']) . '\' ';
		$r = xtc_db_query($sql);
		$new_customer = xtc_db_fetch_array($r);

		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_CUSTOMER_ID_CHANGED, $customer['customers_lastname'].', '. $customer['customers_firstname'], $new_customer['customers_lastname'].', '. $new_customer['customers_firstname']);
		if($orders_id != 0)
		{
			$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_ORDERS_ID_DELETED, $row['orders_id']);
			$orders_id = 0;
		}
		$exSql = true;
	}
	else
	{
		$customers_id = $row['customer'];
	}
	
	if($_POST['products_id'] != '' && $row['products_id'] == 0)
	{
		$products_id = $_POST['products_id'];
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_PRODUCTS_ID_ADDED, $_POST['products_id']);
		$exSql = true;
	}
	elseif($_POST['products_id'] != '' && $row['products_id'] != $_POST['products_id'])
	{
		$products_id = $_POST['products_id'];
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_PRODUCTS_ID_CHANGED, $row['products_id'], $_POST['products_id']);
		$exSql = true;
	}
	elseif($_POST['products_id'] == '' && $row['products_id'] != 0)
	{
		$products_id = 0;
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_PRODUCTS_ID_DELETED, $row['products_id']);
		$exSql = true;
	}
	else
	{
		$products_id = $row['products_id'];
	}
	
	
	if($_POST['department_id'] != '' && $row['department_id'] != $_POST['department_id'])
	{
		$department_id = $_POST['department_id'];
		$sql = 'SELECT department_name FROM ' . TABLE_HELPDESK_DEPARTMENTS . ' WHERE department_id = \''. xtc_db_input($_POST['department_id']) . '\' AND languages_id = \'' . $_SESSION['languages_id'] . '\'';
		$r = xtc_db_query($sql);
		$department = xtc_db_fetch_array($r);
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_DEPARTMENT_ID_CHANGED, $row['department_name'], $department['department_name']);
		$exHistorySql = true;
	}
	elseif($_POST['department_id'] != '' && ($row['department_id'] == 0 || $row['department_id'] == ''))
	{
		$department_id = $_POST['department_id'];
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_DEPARTMENT_ID_ADDED, $_POST['department_id']);
		$exHistorySql = true;
	}
	else
	{
		$department_id = $row['department_id'];
	}
	
			
	if($_POST['priority_id'] != '' && $row['priority_id'] != $_POST['priority_id'])
	{
		$priority_id = $_POST['priority_id'];
		$sql = 'SELECT priority_name FROM ' . TABLE_HELPDESK_TICKETS_PRIORITY . ' WHERE priority_id = \''. xtc_db_input($_POST['priority_id']) . '\' AND languages_id = \'' . $_SESSION['languages_id'] . '\'';
		$r = xtc_db_query($sql);
		$priority = xtc_db_fetch_array($r);
		$auto_comment .= "\n-> ".sprintf(HELPDESK_TEXT_PRIORITY_ID_CHANGED, $row['priority_name'], $priority['priority_name']);
		$exHistorySql = true;
	}
	else
	{
		$priority_id = $row['priority_id'];
	}
	
	$comment = '';
	if($_POST['comment'] != '')
	{
		$comment = $_POST['comment'] . "\n\n";
		$exHistorySql = true;
	}
	
	if(HELPDESK_TICKETS_MAKE_AUTO_COMMENTS == 'True' && $auto_comment != '')
	{
		$comment .= HELPDESK_TEXT_AUTO_COMMENT."\n".substr($auto_comment, 1);
		$exHistorySql = true;
	}
	
	
	
	if($exHistorySql || $exSql)
	{
		$sql = 'UPDATE ' . TABLE_HELPDESK_TICKETS . ' SET 
					customers_id = \''.xtc_db_input($customers_id) . '\',
					products_id = \''.xtc_db_input($products_id) . '\',
					orders_id = \''.xtc_db_input($orders_id) . '\'
				WHERE ticket_id = \''. (int)$_GET['tId'] . '\'';
		xtc_db_query($sql);
		
		if($exHistorySql)
		{
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
					internal,
					add_by
				)
				VALUES
				(
					\'' . xtc_db_input($_SESSION['customer_id']) . '\',
					\'' . xtc_db_input($_GET['tId']) . '\',
					\'' . xtc_db_input($row['status_id']) . '\',
					NOW(),
					\'' . xtc_db_input($department_id) . '\',
					\'' . xtc_db_input($priority_id) . '\',
					\'' . xtc_db_input($comment) . '\',
					\'1\',
					\'supporter\'
				)
				';
			xtc_db_query($sql);
		}
	}
}
elseif(isset($_POST['send']))
{
	if($_POST['comment'] != HELPDESK_TEXT_ENTER_HERE_QUESTION)
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
				th.last_modified DESC
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
				internal,
				add_by
			)
			VALUES
			(
				\'' . xtc_db_input($_SESSION['customer_id']) . '\',
				\'' . xtc_db_input($_GET['tId']) . '\',
				\'' . xtc_db_input($_POST['status_id']) . '\',
				NOW(),
				\'' . xtc_db_input($row['department_id']) . '\',
				\'' . xtc_db_input($row['priority_id']) . '\',
				\'' . xtc_db_input($_POST['comment']) . '\',
				\'' . xtc_db_input($_POST['internal']) . '\',
				\'supporter\'
			)
			';
		xtc_db_query($sql);
	
		# Send E-Mails:
		$smarty = new Smarty;
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = false;

		// set dirs manual
		$smarty->template_dir = DIR_FS_CATALOG.'templates';
		$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
		$smarty->config_dir = DIR_FS_CATALOG.'lang';

		$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

		$smarty->assign('ticket_no', $_GET['tId']);
		$smarty->assign('ticket_link', HTTPS_CATALOG_SERVER.DIR_WS_CATALOG.'helpdesk.php?action=view_ticket&key='.generateKeyForTicket($_GET['tId']).'&tId='.$_GET['tId']);
		
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
		
		if(isset($row['customers_id']) && $row['customers_id'] != '')
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
		
		// To customer:
		if(HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_ANSWER == 'True' && $_POST['internal'] != '1')
		{
			// Text:
			$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/customer_new_answer.html');
			$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/customer_new_answer.txt');
			
			// Subject:
			$subject = str_replace('{$ticket_no}', $_GET['tId'], HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_ANSWER_SUBJECT);
			
			// Send mail:
			xtc_php_mail(
					HELPDESK_TICKETS_EMAIL_FROM_ADDRESS,
					HELPDESK_TICKETS_EMAIL_FROM_NAME,
					$customerEmailAddress,
					$customerName,
					'',
					HELPDESK_TICKETS_EMAIL_REPLY_ADDRESS,
					HELPDESK_TICKETS_EMAIL_REPLY_NAME,
					'',
					'',
					$subject,
					$html_mail,
					$txt_mail
				);
		}
	}
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/modules/helpdesk/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
        <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
            <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <!-- left_navigation //-->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof //-->
            </table>
        </td>
        <!-- body_text //-->
        <td class="boxCenter" width="100%" valign="top">
        	<table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td width="100%">
                	<table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr> 
                            <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_modules.gif'); ?></td>
                            <td class="pageHeading">My Helpdesk</td>
                        </tr>
                        <tr> 
                            <td class="main" valign="top">customweb GmbH</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="mainframe">
<?php
if($filename == 'helpdesk.php')
{
	require_once 'helpdesk_menu.php';
}

// Back button:
echo '<a class="button" href="'. xtc_href_link($filename, xtc_get_all_get_params(array('tId', 'page')) ) . '">'. BUTTON_BACK .'</a><br /><br />';

// Ticket:
$sql = '
		SELECT 
			*,
			th.customers_id AS writer,
			t.customers_id AS customer
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
			th.last_modified ASC
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
	
	if($row['add_by'] != 'supporter')
		$customer_last = $row;
	
	$last = $row;
}

echo '<form action="' . xtc_href_link($filename, xtc_get_all_get_params()) . '" method="POST">';
	echo '<table class="list" style="min-width: 50%">';
		echo '<tr>';
			echo '<th width="30%">' . HELPDESK_TEXT_VIEW_TICKET_ID . ':</th>';
			echo '<td>' . $_GET['tId'] . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th>' . HELPDESK_TEXT_VIEW_TICKET_CUSTOMER . ':</th>';
			if(isset($_POST['search']) && isset($_POST['search_customer']))
			{
				$strSearch = strip_tags($_POST['search_customer']);
				$tok = strtok($strSearch, ',; ');
				$where = '';
				while ($tok !== false)
				{
					$where .= 'customers_firstname LIKE \'%' . $tok . '%\' OR ';
					$where .= 'customers_lastname LIKE \'%' . $tok . '%\' OR ';
					//$where .= 'customers_email_address LIKE \'%' . $tok . '%\' OR ';
					$tok = strtok(',; ');
				}
				
				if($where != '')
				{
					$where = substr($where, 0, -3);
					$sql = '
						SELECT 
							customers_id, 
							customers_lastname,
							customers_firstname,
							customers_email_address 
						FROM 
							' . TABLE_CUSTOMERS . '
						WHERE 
							' . $where . ' 
						ORDER BY
							customers_lastname ASC,
							customers_firstname ASC
						';
					$rs = xtc_db_query($sql);
					echo '<td><select name="customers_id">';
					while($row = xtc_db_fetch_array($rs))
					{
						echo '<option value="' . $row['customers_id'] . '">';
							echo $row['customers_lastname'] . ', ' . $row['customers_firstname'];
						echo '</option>';
					}
					echo '</select> <input type="submit" name="cancel" value="'.BUTTON_CANCEL . '" /></td>';
				}
			}
			else
			{
				if(isset($last['customer']) && $last['customer'] != '0')
				{
					$sql = 'SELECT * FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = \'' . xtc_db_input($last['customer']) . '\'';
					$r = xtc_db_query($sql);
					$writer = xtc_db_fetch_array($r);
					echo '<td>';
						echo '<a href="' . xtc_href_link(FILENAME_CUSTOMERS, 'cID='.$last['customer'].'&action=edit').'">';
						echo $writer['customers_lastname'] . ', '. $writer['customers_firstname'] . '</a>';
						echo ' (<a href="mailto:'.$writer['customers_email_address'].'">'.$writer['customers_email_address'].'</a>)';
					echo '</td>';
				}
				else
				{
					echo '<td>' . $last['customers_lastname'] . ', ' . $last['customers_firstname'] . ' (<a href="mailto:'.$last['customers_email'].'">'.$last['customers_email'].'</a>)</td>';
					$cSearchString = $last['customers_firstname'] .' ' . $last['customers_lastname'];
				}
				echo '<td>';
					echo '<input name="search_customer" type="text" value="'.$cSearchString .'" /> ';
					echo '<input name="search" type="submit" value="'.BUTTON_SEARCH .'" />';
				echo '</td>';
			}
		echo '</tr>';
		
		if(isset($last['customer']) && $last['customer'] != '0')
		{
			echo '<tr>';
				echo '<th>' . HELPDESK_TEXT_VIEW_TICKET_ORDER_ID . ':</th>';
				echo '<td>';
					echo '<select name="orders_id">';
						echo '<option value="">' . HELPDESK_TEXT_NO_ORDER_CONNECTED . '</option>';
					$sql = '
							SELECT 
								*,
								ot.text AS order_total
							FROM
								' . TABLE_ORDERS . ' AS o,
								' . TABLE_ORDERS_TOTAL . ' AS ot
							WHERE
								o.customers_id = \'' . $last['customer'] . '\'
							  AND
								ot.orders_id = o.orders_id
							  AND
								ot.class = \'ot_total\'
							';
					$rs = xtc_db_query($sql);
					if(xtc_db_num_rows($rs) > 0)
					{
						while($row = xtc_db_fetch_array($rs))
						{
							echo '<option value="'.$row['orders_id'].'" ';
							if($row['orders_id'] == $_POST['orders_id'] or (!isset($_POST['orders_id']) && $last['orders_id'] == $row['orders_id']))
							{
								echo ' selected="selected" ';
							}
							echo '>';
								echo xtc_date_short($row['date_purchased']) . ' - ' . strip_tags($row['order_total']) . ' (#'.$row['orders_id'].')';
							echo '</option>';
						}
					}
					echo '</select>';
					if($_POST['orders_id'] != '')
					{
						echo ' <a href="'.xtc_href_link(FILENAME_ORDERS, 'oID='.$_POST['orders_id'].'&action=edit') .'">'.HELPDESK_TEXT_VIEW_TICKET_ORDER_ID_VIEW.'</a>';
					}
					elseif($last['orders_id'] != 0)
					{
						echo ' <a href="'.xtc_href_link(FILENAME_ORDERS, 'oID='.$last['orders_id'].'&action=edit') .'">'.HELPDESK_TEXT_VIEW_TICKET_ORDER_ID_VIEW.'</a>';
					}
				echo '</td>';
			echo '</tr>';
		}
		
		echo '<tr>';
			echo '<th>' . HELPDESK_TEXT_VIEW_TICKET_PRODUCT_ID . ':</th>';
			if($last['products_id'] != 0)
			{
				echo '<td>
						<input type="text" name="products_id" size="5" value="' . $last['products_id'] . '" />
						<a href="'.xtc_href_link(FILENAME_CATEGORIES, 'pID='.$last['products_id'].'&action=new_product') .'">'.HELPDESK_TEXT_VIEW_TICKET_PRODUCT_ID_VIEW.'</a>
					</td>';
			}
			else
			{
				echo '<td>
						<input type="text" name="products_id" size="5" value="" />
					</td>';
			}
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>' . HELPDESK_TEXT_VIEW_TICKET_DEPARTMENT . ':</th>';
			echo '<td><select name="department_id">';
			$sql = 'SELECT * FROM ' . TABLE_HELPDESK_DEPARTMENTS . ' WHERE languages_id = '.(int) $_SESSION['languages_id'] . ' ORDER BY sort_order';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				echo '<option value="'.$row['department_id'] . '" ';
				if($last['department_id'] == $row['department_id'])
					echo ' selected="selected" ';
				echo '>'.$row['department_name'] . '</option>';
			}
			echo '</select></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>' . HELPDESK_TEXT_VIEW_TICKET_PRIORITY . ':</th>';
			echo '<td><select name="priority_id">';
			$sql = 'SELECT * FROM ' . TABLE_HELPDESK_TICKETS_PRIORITY . ' WHERE languages_id = '.(int) $_SESSION['languages_id'] . ' ORDER BY sort_order';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				echo '<option value="'.$row['priority_id'] . '" ';
				if($last['priority_id'] == $row['priority_id'])
					echo ' selected="selected" ';
				echo '>'.$row['priority_name'] . '</option>';
			}
			echo '</select></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>' . HELPDESK_TEXT_VIEW_TICKET_COMMENT . ':</th>';
			echo '<td><textarea name="comment" cols="25" rows="5"></textarea></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="3">';
			echo '<input type="submit" name="save" value="'.BUTTON_SAVE.'" /> ';
			echo '<input type="submit" name="cancel" value="'.BUTTON_CANCEL.'" />';
			echo '</td>';
		echo '</tr>';
	
	echo '</table>';
echo '</form>';


echo '<form action="' . xtc_href_link($filename, xtc_get_all_get_params()) . '" method="POST">';

foreach($ticket as $data)
{
	if ($data['add_by'] == 'customer')
		$class = 'ticket_history float_left';
	else
		$class = 'ticket_history float_right';
	
	if($data['internal'] == 1)
		$class .= ' internal';

	echo '<div class="'.$class.'">';
	
		echo '<div class="title">'.$data['title'].'</div>';
		echo '<div class="comment">'.$data['comment'].'</div>';
		echo '<div class="date">'.$data['date'].'</div>';
	echo '</div>';
}


	echo '<div class="ticket_history float_right">';
	
		echo '<div class="title"><strong>'.HELPDESK_TEXT_VIEW_TICKET_ANSWER.'</strong></div>';
		echo '<div class="comment">';
		echo '<textarea name="comment" style="width:100%;" rows="20" onfocus="if(this.innerHTML == \''.HELPDESK_TEXT_ENTER_HERE_QUESTION.'\') this.innerHTML = \'\';" onblur="if(this.innerHTML ==\'\') this.innerHTML = \''.HELPDESK_TEXT_ENTER_HERE_QUESTION.'\';">';
			echo HELPDESK_TEXT_ENTER_HERE_QUESTION;
		echo '</textarea>';
		echo '</div>';
		echo '<div class="date">';
			echo '<div style="float:left; text-align: right;"> <input type="checkbox" id="internal" name="internal" value="1" /></div> ';
			echo '<div style="float:left; text-align: right; padding: 2px 2px 0px 2px;"><label for="internal">'.HELPDESK_TEXT_VIEW_TICKET_INTERNAL.'</label></div> ';
			echo '<div style="float:left; text-align: right; padding: 0px 2px 0px 10px;"><select name="status_id">';
				
			$sql = 'SELECT * FROM ' . TABLE_HELPDESK_TICKETS_STATUS . ' WHERE languages_id = '.(int) $_SESSION['languages_id'] . ' ORDER BY sort_order';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				echo '<option value="'.$row['status_id'] . '" ';
				if(HELPDESK_TICKETS_DEFAULT_CLOSED_STATUS == $row['status_id'])
					echo ' selected="selected" ';
				echo '>'.$row['status_name'] . '</option>';
			}

				
			echo '</select></div>';	
			echo ' <input type="submit" name="send" value="'.BUTTON_SEND.'" />';	
		echo '</div>';
	echo '</div>';

echo '</form>';

?>


                </td>
            </tr>
         </table>
      </td>
   </tr>
</table>
</body>
</html>