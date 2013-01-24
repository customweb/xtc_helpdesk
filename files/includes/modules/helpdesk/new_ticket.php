<?php
/**
::Header::
 */

// -> find out if product was searching
if(isset($_POST['product_search_search_x']) || isset($_POST['product_search_delete_x']))
{
	$productIsSearching = true;
}
else
{
	$productIsSearching = false;
}

$sql = '
		SELECT 
			*
		FROM
			' . TABLE_HELPDESK_TICKETS_PRIORITY . ' AS s
		WHERE
			s.languages_id = \'' . (int) $_SESSION['languages_id'] . '\'
		ORDER BY
			sort_order
		';
$rs = xtc_db_query($sql);
$priorityDropdown[] = array('id' => 'false', 'text' => '- ' . HELPDESK_TEXT_PLEASE_SELECT . ' -');
while($row = xtc_db_fetch_array($rs))
{
	$priorityDropdown[]  = array('id' => $row['priority_id'], 'text' => $row['priority_name']);
}

$sql = '
		SELECT 
			*
		FROM
			' . TABLE_HELPDESK_DEPARTMENTS . ' AS s
		WHERE
			s.languages_id = \'' . (int) $_SESSION['languages_id'] . '\'
		ORDER BY
			sort_order
		';
$rs = xtc_db_query($sql);
$departmentsDropdown[] = array('id' => 'false', 'text' => '- ' . HELPDESK_TEXT_PLEASE_SELECT . ' -');
while($row = xtc_db_fetch_array($rs))
{
	$departmentsDropdown[] = array('id' => $row['department_id'], 'text' => $row['department_name']);
}

 
# if is need, redirect to login:
if(HELPDESK_TICKETS_NEW_TICKET_LOGIN == 'True' && !isset ($_SESSION['customer_id']))
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));

$smarty = new Smarty;
# include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

# Insert new ticket:
if(isset($_POST['comment']) && $productIsSearching === false)
{
    $error = false;
    $_POST['comment'] = xtc_db_prepare_input($_POST['comment']);
    $_POST['department_id'] = xtc_db_prepare_input($_POST['department_id']);
    $_POST['priority_id'] = xtc_db_prepare_input($_POST['priority_id']);
    $_POST['orders_id'] = xtc_db_prepare_input($_POST['orders_id']);
    
    if($_POST['comment'] == HELPDESK_TEXT_ENTER_HERE_QUESTION || $_POST['comment'] == '')
    {
        $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_NO_COMMENT);
        $error = true;
    }
    
    if($_POST['department_id'] == 'false')
    {
        $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_NO_DEPARTMENT);
        $error = true;
    }
        
    if($_POST['priority_id'] == 'false' or !isset($_POST['priority_id']))
    {
        if(HELPDESK_TICKETS_CUSTOMER_SET_PRIORITY == 'True')
		{
			$messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_NO_PRIORITY);
			$error = true;
		}
		else
		{
			$_POST['priority_id'] = HELPDESK_TICKETS_DEFAULT_PRIORITY;
		}
    }
    
    if(!isset($_SESSION['customer_id']))
    {
        $_POST['customers_firstname'] = xtc_db_prepare_input($_POST['customers_firstname']);
        $_POST['customers_lasttname'] = xtc_db_prepare_input($_POST['customers_lastname']);
        $_POST['customers_email'] = xtc_db_prepare_input($_POST['customers_email']);
        
        if(strlen($_POST['customers_firstname']) < ENTRY_FIRST_NAME_MIN_LENGTH )
        {
            $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_FIRSTNAME);
            $error = true;
        }
        if(strlen($_POST['customers_lastname']) < ENTRY_LAST_NAME_MIN_LENGTH )
        {
            $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_LASTNAME);
            $error = true;
        }
        
        if(strlen($_POST['customers_email']) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH )
        {
            $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_EMAIL);
            $error = true;
        }
        elseif(xtc_validate_email($_POST['customers_email']) == false)
        {
            $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_EMAIL);
            $error = true;
        }
		else
		{
			$sql = 'SELECT * FROM '.TABLE_CUSTOMERS.' WHERE customers_email_address = \'' . xtc_db_input($_POST['customers_email']) . '\'';
			$rs = xtc_db_query($sql);
			if($row = xtc_db_fetch_array($rs))
			{
				$messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_LOGIN);
				$error = true;
			}
		}
		
    }
    
    
    if(
        ((HELPDESK_TICKETS_CAPTCHA == 'NoWhenLogin' && !isset($_SESSION['customer_id'])) || HELPDESK_TICKETS_CAPTCHA == 'Yes') 
      &&
        ($_SESSION['vvcode'] != trim($_POST['vvcode']) || empty($_SESSION['vvcode']))
      )
    {
        $messageStack->add('new_ticket', HELPDESK_TEXT_ERROR_CAPTCHA);
        $error = true;
    }
                
    
    # add it to the database:
    if($error == false)
    {
        
        $sql = '
            INSERT INTO 
                ' . TABLE_HELPDESK_TICKETS . '
            (
                customers_id,
                customers_firstname,
                customers_lastname,
                customers_email,
                orders_id,
				products_id,
                languages_id,
                ticket_date
            )
            VALUES
            (
                \'' . xtc_db_input($_SESSION['customer_id']) . '\',
                \'' . xtc_db_input($_POST['customers_firstname']) . '\',
                \'' . xtc_db_input($_POST['customers_lastname']) . '\',
                \'' . xtc_db_input($_POST['customers_email']) . '\',
                \'' . xtc_db_input($_POST['orders_id']) . '\',
                \'' . xtc_db_input($_POST['products_id']) . '\',
                \'' . (int) $_SESSION['languages_id'] . '\',
                NOW()
            )
            ';
        xtc_db_query($sql);
        $ticketId = xtc_db_insert_id();
        
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
                \'' . xtc_db_input($_SESSION['customer_id']) . '\',
                \'' . xtc_db_input($ticketId) . '\',
                \'' . xtc_db_input(HELPDESK_TICKETS_DEFAULT_STATUS) . '\',
                NOW(),
                \'' . xtc_db_input($_POST['department_id']) . '\',
                \'' . xtc_db_input($_POST['priority_id']) . '\',
                \'' . xtc_db_input($_POST['comment']) . '\',
                \'customer\'
            )
            ';
        xtc_db_query($sql);
		
		# Send E-Mails:
		$smarty->assign('ticket_no', $ticketId);
		if(isset($_SESSION['customer_id']))
		{
			$sql = 'SELECT * FROM '.TABLE_CUSTOMERS.' WHERE customers_id = \'' . xtc_db_input($_SESSION['customer_id']) . '\'';
			$rs = xtc_db_query($sql);
			$c = xtc_db_fetch_array($rs);
			$customerName = $c['customers_firstname'] . ' ' . $c['customers_lastname'];
			$customerEmailAddress = $c['customers_email_address'];
		}
		else
		{
			$customerName = $_POST['customers_firstname'] . ' ' . $_POST['customers_lastname'];
			$customerEmailAddress = $_POST['customers_email'];
		}
		
		// To customer:
		if(HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_TICKET == 'True')
		{
			// Text:
			$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/customer_new_ticket.html');
			$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/customer_new_ticket.txt');
			
			// Subject:
			$subject = str_replace('{$ticket_no}', $ticketId, HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_TICKET_SUBJECT);
			
			// Send mail:
			xtc_php_mail(
					HELPDESK_TICKETS_EMAIL_FROM_ADDRESS,
					HELPDESK_TICKETS_EMAIL_FROM_NAME,
					$customerEmailAddress,
					$customerNam,
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
		
		// To staff:
		if(HELPDESK_TICKETS_EMAIL_STAFF_NEW_TICKET == 'True')
		{
			// Text:
			$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/staff_new_ticket.html');
			$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/helpdesk/staff_new_ticket.txt');
			
			// Subject:
			$subject = str_replace('{$ticket_no}', $ticketId, HELPDESK_TICKETS_EMAIL_STAFF_NEW_TICKET_SUBJECT);
			
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
		
		if(isset($_SESSION['customer_id']))
        	xtc_redirect(xtc_href_link(FILENAME_HELPDESK, 'action=view_ticket&new=1&tId='.$ticketId, 'SSL'));
		else
        	xtc_redirect(xtc_href_link(FILENAME_HELPDESK, 'action=view_ticket&new=1&tId='.$ticketId.'&key='.generateKeyForTicket($ticketId), 'SSL'));
    }
        
}

# Navbar:
$breadcrumb->add(NAVBAR_HELPDESK, xtc_href_link(FILENAME_HELPDESK, '', 'SSL'));
$breadcrumb->add(NAVBAR_HELPDESK_NEW_TICKET, xtc_href_link(FILENAME_HELPDESK, 'action=new_ticket', 'SSL'));

require (DIR_WS_INCLUDES.'header.php');

# Add variables:
// IsLogin:
if(isset($_SESSION['customer_id']))
    $smarty->assign('IS_LOGIN', '1');
else
    $smarty->assign('IS_LOGIN', '0');

# Add Orders:
if(isset($_SESSION['customer_id']))
{
    $sql = '
            SELECT 
                *,
                ot.text AS order_total
            FROM
                ' . TABLE_ORDERS . ' AS o,
                ' . TABLE_ORDERS_TOTAL . ' AS ot
            WHERE
                o.customers_id = \'' . xtc_db_input($_SESSION['customer_id']) . '\'
              AND
                ot.orders_id = o.orders_id
              AND
                ot.class = \'ot_total\'
            ';
    $rs = xtc_db_query($sql);
    if(xtc_db_num_rows($rs) > 0)
    {
        $ordersDropdown[] = array('id' => '', 'text' => '- ' . HELPDESK_TEXT_PLEASE_SELECT_OPTIONAL . ' -');
        while($row = xtc_db_fetch_array($rs))
        {
            $ordersDropdown[] = array('id' => $row['orders_id'], 'text' => xtc_date_short($row['date_purchased']) . ' - ' . strip_tags($row['order_total']) . ' (#'.$row['orders_id'].')' );
        }
        
		if(isset($_POST['orders_id']))
			$oId = $_POST['orders_id'];
		elseif(isset($_GET['orders_id']))
			$oId = $_GET['orders_id'];
		
        $smarty->assign('ORDERS_DROPDOWN', xtc_draw_pull_down_menu('orders_id', $ordersDropdown, $oId));
    }
}

# Add main heading:
$smarty->assign('MAIN_HEADING', HELPDESK_MENU_NEW_TICKET);

# Add some languages phrases:
$smarty->assign('TEXT_FRISTNAME', HELPDESK_NEW_TICKET_FIRSTNAME);
$smarty->assign('TEXT_LASTNAME', HELPDESK_NEW_TICKET_LASTNAME);
$smarty->assign('TEXT_EMAIL', HELPDESK_NEW_TICKET_EMAIL);
$smarty->assign('TEXT_PRIORITY', HELPDESK_TEXT_PRIORITY);
$smarty->assign('TEXT_DEPARTMENT', HELPDESK_TEXT_DEPARTMENT);
$smarty->assign('TEXT_ORDER', HELPDESK_TEXT_ORDER);
$smarty->assign('TEXT_PRODUCT', HELPDESK_TEXT_PRODUCT);
$smarty->assign('TEXT_COMMENT', HELPDESK_TEXT_COMMENT);

# Add firstname, lastname & email input fields:
if(isset($_SESSION['customer_id']))
{
    $sql = 'SELECT customers_email_address FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = \'' .  xtc_db_input($_SESSION['customer_id']) . '\'';
    $rs = xtc_db_query($sql);
    $row = xtc_db_fetch_array($rs);
    $smarty->assign('FRISTNAME_FIELD', $_SESSION['customer_first_name']);
    $smarty->assign('LASTNAME_FIELD', $_SESSION['customer_last_name']);
    $smarty->assign('EMAIL_FIELD', $row['customers_email_address']);
}
else
{
    $smarty->assign('FRISTNAME_FIELD', xtc_draw_input_field('customers_firstname', $_POST['customers_firstname']));
    $smarty->assign('LASTNAME_FIELD', xtc_draw_input_field('customers_lastname', $_POST['customers_lastname']));
    $smarty->assign('EMAIL_FIELD', xtc_draw_input_field('customers_email', $_POST['customers_email']));
}

# Add products_id:
// -> unset product_ids
if(isset($_POST['product_search_delete_x']))
{
	unset($_POST['products_id'], $_GET['products_id'], $_POST['product_search']);
}
// -> When product_id is set:
if(isset($_GET['products_id']) or isset($_POST['products_id']))
{
	if(isset($_GET['products_id']))
		$pId = $_GET['products_id'];
	else
		$pId = $_POST['products_id'];
		
		$sql = '
		SELECT
			p.products_id,
			products_name,
			products_model
		FROM
			'. TABLE_PRODUCTS . ' AS p,
			'. TABLE_PRODUCTS_DESCRIPTION . ' AS pd
		WHERE
			p.products_id = pd.products_id
		  AND
			p.products_id = \'' . xtc_db_input($pId) . '\'
		  AND
			pd.language_id = '.(int)$_SESSION['languages_id'] . '
		';
	$rs = xtc_db_query($sql);
	$row = xtc_db_fetch_array($rs);
	$text = $row['products_name'];
	if($row['products_model'] != '')
	{
		$text .= ' ('.$row['products_model'].')';
	}
	$smarty->assign('PRODUCT_FIELD', xtc_draw_hidden_field('products_id', $pId).$text);
	$smarty->assign('PRODUCT_DELETE_BUTTON', xtc_image_submit('small_delete.gif', IMAGE_BUTTON_DELETE, 'name="product_search_delete"'));
}
// -> When it must be shown a search box:
elseif(!isset($_POST['product_search']) || empty($_POST['product_search']))
{
	$smarty->assign('PRODUCT_FIELD', xtc_draw_input_field('product_search','')); 
	$smarty->assign('PRODUCT_SEARCH_BUTTON', xtc_image_submit('button_quick_find.gif', IMAGE_BUTTON_SEARCH, 'name="product_search_search"'));
}
// -> When a select box must be shown:
else
{
	if (GROUP_CHECK == 'true')
	{
		$group_check = "AND group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 ";
	}
	
	$sql = '
		SELECT
			p.products_id,
			products_name,
			products_model
		FROM
			'. TABLE_PRODUCTS . ' AS p,
			'. TABLE_PRODUCTS_DESCRIPTION . ' AS pd
		WHERE
			p.products_id = pd.products_id
		  AND
			pd.language_id = '.(int)$_SESSION['languages_id'] . '
		  AND
			products_status = \'1\'
		  AND
			(
				pd.products_name LIKE \'%'.$_POST['product_search'] .'%\'
			  OR
				p.products_model LIKE \'%'.$_POST['product_search'] .'%\'
			)
			' . $group_check . '
		';
	$rs = xtc_db_query($sql);
	while($row = xtc_db_fetch_array($rs))
	{
		$text = $row['products_name'];
		if($row['products_model'] != '')
		{
			$text .= ' ('.$row['products_model'].')';
		}
		$productsDropdown[] = array('id' => $row['products_id'], 'text' => $text);
	}
	if(count($productsDropdown) > 0)
	{
		$smarty->assign('PRODUCT_FIELD', xtc_draw_pull_down_menu('products_id', $productsDropdown, $_POST['products_id']));
		$smarty->assign('PRODUCT_DELETE_BUTTON', xtc_image_submit('small_delete.gif', IMAGE_BUTTON_DELETE, 'name="product_search_delete"'));
	}
	// -> else show search box:
	else
	{
		$smarty->assign('PRODUCT_FIELD', xtc_draw_input_field('product_search', $_POST['product_search'])); 
		$smarty->assign('PRODUCT_SEARCH_BUTTON', xtc_image_submit('button_quick_find.gif', IMAGE_BUTTON_SEARCH, 'name="product_search_search"'));
	}
}


# Add comment field:
if(isset($_POST['comment']))
    $comment = $_POST['comment'];
else
    $comment = HELPDESK_TEXT_ENTER_HERE_QUESTION;
$txtArea = '<textarea style="width:100%;" rows="16" name="comment" 
			onfocus="if(this.innerHTML == \''.HELPDESK_TEXT_ENTER_HERE_QUESTION.'\') this.innerHTML = \'\';" onblur="if(this.innerHTML ==\'\') this.innerHTML = \''.HELPDESK_TEXT_ENTER_HERE_QUESTION.'\';">'.$comment.'</textarea>';
$smarty->assign('COMMENT_FIELD', $txtArea);

# Add Save Button:
$smarty->assign('SEND_BUTTON', xtc_image_submit('button_send.gif', IMAGE_BUTTON_SEND) );


# Add departments
if(HELPDESK_TICKETS_QUESTION_ORDER_DEFAULT_DEPARTMENT != '' && ((isset($_GET['orders_id']) && !empty($_GET['orders_id'])) || (isset($_POST['orders_id']) && !empty($_POST['orders_id'])) ) )
{
	$sql = 'SELECT * FROM ' . TABLE_HELPDESK_DEPARTMENTS . ' WHERE languages_id = \'' . (int) $_SESSION['languages_id'] . '\' AND department_id = \'' . HELPDESK_TICKETS_QUESTION_ORDER_DEFAULT_DEPARTMENT . '\'';
	$rs = xtc_db_query($sql);
	$dep = xtc_db_fetch_array($rs);
	$smarty->assign('DEPARTMENT_DROPDOWN', xtc_draw_hidden_field('department_id', HELPDESK_TICKETS_QUESTION_ORDER_DEFAULT_DEPARTMENT).$dep['department_name']);
}
elseif(HELPDESK_TICKETS_QUESTION_PRODUCT_DEFAULT_DEPARTMENT != '' && ((isset($_GET['products_id']) && !empty($_GET['products_id'])) || (isset($_POST['products_id']) && !empty($_POST['products_id'])) ) )
{
	$sql = 'SELECT * FROM ' . TABLE_HELPDESK_DEPARTMENTS . ' WHERE languages_id = \'' . (int) $_SESSION['languages_id'] . '\' AND department_id = \'' . HELPDESK_TICKETS_QUESTION_PRODUCT_DEFAULT_DEPARTMENT . '\'';
	$rs = xtc_db_query($sql);
	$dep = xtc_db_fetch_array($rs);
	$smarty->assign('DEPARTMENT_DROPDOWN', xtc_draw_hidden_field('department_id', HELPDESK_TICKETS_QUESTION_PRODUCT_DEFAULT_DEPARTMENT).$dep['department_name']);
}
else
{
	$smarty->assign('DEPARTMENT_DROPDOWN', xtc_draw_pull_down_menu('department_id', $departmentsDropdown, $_POST['department_id']));
}

# Priority
if(HELPDESK_TICKETS_CUSTOMER_SET_PRIORITY == 'True')
{
	$smarty->assign('PRIORITY_DROPDOWN', xtc_draw_pull_down_menu('priority_id', $priorityDropdown, $_POST['priority_id']));
}

# Add captcha
if((HELPDESK_TICKETS_CAPTCHA == 'NoWhenLogin' && !isset($_SESSION['customer_id'])) or HELPDESK_TICKETS_CAPTCHA == 'Yes')
{
    $smarty->assign('CAPTCHA', '<img src="'.FILENAME_DISPLAY_VVCODES.'?'.session_name().'='.session_id().'" />');
    $smarty->assign('CAPTCHA_INPUT',  xtc_draw_input_field('vvcode', '', 'size="6" maxlenght="6"', 'text', false));
    $smarty->assign('TEXT_CAPTCHA', HELPDESK_TEXT_CAPTCHA);
}

# Add form:
$smarty->assign('FORM_HEAD', xtc_draw_form('checkout_confirmation', xtc_href_link(FILENAME_HELPDESK, xtc_get_all_get_params(), 'SSL'), 'post'));
$smarty->assign('FORM_FOOTER', '</form>');

# Message Stack
if ($messageStack->size('new_ticket') > 0)
{
	$smarty->assign('MESSAGE', $messageStack->output('new_ticket'));
}

# Generate main Content:
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/helpdesk/new_ticket.html');

# Generate index:
$smarty->assign('main_content', $main_content);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;

# Output html:
$smarty->display(CURRENT_TEMPLATE.'/index.html');
?>