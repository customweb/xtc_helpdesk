<?php
/**
::Header::
 */

// check if the user is a stuf:
if($filename != 'helpdesk.php')
{
	$sql = 'SELECT * FROM ' . TABLE_HELPDESK_STAFF . ' WHERE customers_id = \'' . (int) $_SESSION['customer_id'] . '\'';
	$rs = xtc_db_query($sql);
	
	if(($staff = xtc_db_fetch_array($rs)) === false)
	{
		xtc_redirect(xtc_href_link(FILENAME_HELPDESK, ''));
	}
}
else
{	
	$staff = array(
					'departments' => 'all',
					'languages' => 'all',
					'order_by' => 'status'
				);
}

if(!isset($_GET['site']))
	$_GET['site'] = 0;
	
if(!isset($_GET['itemsPerSite']))
	$_GET['itemsPerSite'] = 10;

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

// department where:
$deps = parseStringToArray($staff['departments']);
$departmentWhere = '';
if(is_array($deps))
{
	foreach($deps as $id)
	{
		$departmentWhere .= 'th.department_id = ' . (int)$id . ' OR ';
	}
	$departmentWhere = '('.substr($departmentWhere, 0, -3).') AND';
}

$langs = parseStringToArray($staff['languages']);
$languagesWhere = '';
if(is_array($langs))
{
	foreach($langs as $id)
	{
		$languagesWhere .= 't.languages_id = ' . (int)$id . ' OR ';
	}
	$languagesWhere = '('.substr($languagesWhere, 0, -3).') AND';
}
$where = '';
if($departmentWhere != '' || $languagesWhere != '')
{
	$str = $departmentWhere.$languagesWhere;
	if($str != '')
		$str = substr($str, 0, -4);
	$where = '
	AND
	(
		(
			th.customers_id = '.(int)$_SESSION['customer_id'] .'
		  AND
			th.add_by = \'supporter\'
		)
		OR ('.$str.')
	)
	';
}

if(isset($_GET['order_by']) && ($_GET['order_by'] == 'priority' || $_GET['order_by'] == 'status'))
	$_GET['order_by'] = $_GET['order_by'];
else
	$_GET['order_by'] = $staff['order_by'];


if(isset($_GET['mode']) && ($_GET['mode'] == 'asc' || $_GET['mode'] == 'desc'))
	$_GET['mode'] = $_GET['mode'];
else
	$_GET['mode'] = 'desc';

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
			' . $where . '
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
	
	if(!is_array($deps) or in_array($history['department_id'], $deps))
	{
		$row = array_merge($history, $row);
		$tickets[$i] = $row;
		$i++;
	}
}

if($_GET['order_by'] == 'status')
{
	usort($tickets, cmpStatusSort);
}
elseif($_GET['order_by'] == 'priority')
{
	usort($tickets, cmpPrioritySort);
}

if($_GET['mode'] == 'desc')
{
	$newTickets = array();
	end($tickets);
	$i = 0;
	do
	{
		$newTickets[$i] = current($tickets);
		$i++;
	}
	while(prev($tickets) !== false);
	
	$tickets = $newTickets;
}

echo '<form action="'.$filename.'" method="GET">';

for($i = 0; $i < count($tickets); $i += $_GET['itemsPerSite'])
{
	$siteNo = round(($i)/$_GET['itemsPerSite']);
	$sites[] = array('id' => $siteNo, 'text' => sprintf(TEXT_HELPDESK_TEXT_SITE_DROPDOWN, $siteNo+1 ) );
}

$modes[] = array('id' => 'asc', 'text' => TEXT_HELPDESK_ASC);
$modes[] = array('id' => 'desc', 'text' => TEXT_HELPDESK_DESC);

$order_by = array();
$order_by[] = array('id' => 'status', 'text' => TEXT_HELPDESK_SORT_BY_STATUS);
$order_by[] = array('id' => 'priority', 'text' => TEXT_HELPDESK_SORT_BY_PRIORITY);

echo '<table><tr>';
echo '<td>'.xtc_draw_pull_down_menu('order_by', $order_by, $_GET['order_by']).'</td>';
echo '<td>'.xtc_draw_pull_down_menu('mode', $modes, $_GET['mode']).'</td>';
echo '<td>'.xtc_draw_pull_down_menu('site', $sites, $_GET['site']).'</td>';
echo '<td><input name="itemsPerSite" type="text" style="text-align:center;" size="3" value="'.$_GET['itemsPerSite'].'" /></td>';
echo '<td><input type="submit" value="'.BUTTON_VIEW.'" /></td>';
echo '</tr></table>';


echo '<table class="listing" cellspacing="0" cellpadding="0">';
	echo '<th>'.HELPTESK_HEADING_TICKET_ID .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_CREATE_DATE .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_LAST_MODIFIED .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_CUSTOMER .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_STATUS .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_PRIORITY .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_DEPARTMENT .'</th>';
	echo '<th>'.HELPTESK_HEADING_TICKET_EDIT .'</th>';

if(count($tickets) > 0 && isset($tickets[0]['ticket_id']))
{
	for($i = $_GET['site']*$_GET['itemsPerSite']; $i < $_GET['itemsPerSite']+($_GET['site']*$_GET['itemsPerSite']) && $i < count($tickets); $i++)
	{
		$data = $tickets[$i];
		echo '<tr style="background-color:#'.mergeColor($data['priority_color'], $data['status_color']).';">';
			echo '<td>' . $data['ticket_id'] . '</td>';
			echo '<td>' . $data['ticket_date'] . '</td>';
			echo '<td>' . $data['last_modified'] . '</td>';
			if($data['ticket_customers_id'] > 0)
			{
				$sql = 'SELECT * FROM ' . TABLE_CUSTOMERS . ' WHERE customers_id = ' . $data['ticket_customers_id'] . '';
				$rs = xtc_db_query($sql);
				$customer = xtc_db_fetch_array($rs);
				echo '<td>';
					echo '<a href="' . xtc_href_link(FILENAME_CUSTOMERS, 'cID='.$data['ticket_customers_id'].'&action=edit').'">';
					echo $customer['customers_lastname'] . ', ' . $customer['customers_firstname'] . '';
					echo '</a>';
				echo '</td>';
				
			}
			else
			{
				echo '<td>' . $data['customers_lastname'] . ', ' . $data['customers_firstname'] . ' ('.$data['customers_email'] . ')</td>';
			}
			echo '<td>' . $data['status_name'] . '</td>';
			echo '<td>' . $data['priority_name'] . '</td>';
			echo '<td>' . $data['department_name'] . '</td>';
			echo '<td>';
					echo '<a href="' . xtc_href_link($filename, xtc_get_all_get_params(array('tId', 'page')).'tId='.$data['ticket_id'].'&page=edit_ticket').'">';
						echo HELPTESK_HEADING_TICKET_EDIT;
					echo '</a>';
			echo '</td>';
		echo '</tr>';
		
	}
}
echo '</table>';

echo '</form>';

echo '<form action="'.$filename.'" method="GET">';

echo '<input type="hidden" name="order_by" value="'.$_GET['order_by'].'" />';
echo '<input type="hidden" name="mode" value="'.$_GET['mode'].'" />';
echo '<input type="hidden" name="itemsPerSite" value="'.$_GET['itemsPerSite'].'" />';
echo '<table><tr>';
echo '<td>'.xtc_draw_pull_down_menu('site', $sites, $_GET['site']).'</td>';
echo '<td><input type="submit" value="'.BUTTON_VIEW.'" /></td>';
echo '</tr></table>';

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