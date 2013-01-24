<?php
/**
::Header::
 */

if($filename != 'helpdesk.php')
{
	xtc_redirect(FILENAME_MY_HELPDESK);
}

if(isset($_POST['cancel']))
{
	xtc_redirect(xtc_href_link($filename, xtc_get_all_get_params( array('sId', 'action') ) ));
}

switch($_GET['action'])
{
	case 'save':
		if(isset($_POST['save']))
		{
			if(is_array($_POST['departments']))
			{
				$sql = 'SELECT * FROM ' . TABLE_HELPDESK_DEPARTMENTS . ' WHERE languages_id = '.(int)$_SESSION['languages_id'] . ' ORDER BY sort_order';
				$rs = xtc_db_query($sql);
				$sum = 0;
				while($row = xtc_db_fetch_array($rs))
				{
					if(isset($_POST['departments'][$row['department_id']]))
					{
						$departments[] = $row['department_id'];
					}
					$sum++;
				}
				
				if(count($departments) == $sum)
				{
					$departments = 'all';
				}
				else
				{
					$departments = implode(',', $departments);
				}
				
			}
			else
			{
				$departments = '';
			}
			
			if(is_array($_POST['languages']))
			{
				$sql = 'SELECT * FROM ' . TABLE_LANGUAGES . ' ORDER BY sort_order';
				$rs = xtc_db_query($sql);
				$sum = 0;
				while($row = xtc_db_fetch_array($rs))
				{
					if(isset($_POST['languages'][$row['languages_id']]))
					{
						$languages[] = $row['languages_id'];
					}
					$sum++;
				}
				
				if(count($languages) == $sum)
				{
					$languages = 'all';
				}
				else
				{
					$languages = implode(',', $languages);
				}
				
			}
			else
			{
				$languages = '';
			}
			
			if($_POST['order_by'] == 'status')
				$order_by = 'status';
			else
				$order_by = 'priority';
			
			if($_GET['sId'] == 'new')
			{
				$sql = 'INSERT INTO
							' . TABLE_HELPDESK_STAFF . ' 
						(
							customers_id,
							departments,
							languages,
							receive_email,
							order_by
						)
						VALUES
						(
							\'' . xtc_db_input($_POST['customers_id']) . '\',
							\'' . $departments . '\',
							\'' . $languages . '\',
							\'' . xtc_db_input($_POST['receive_email']) . '\',
							\'' . $order_by . '\'
						)
						';
			}
			else
			{
				$sql = '
					UPDATE 
						' . TABLE_HELPDESK_STAFF . '
					SET
						customers_id = \'' . xtc_db_input($_POST['customers_id']) . '\',
						departments = \'' . $departments . '\',
						languages = \'' . $languages . '\',
						receive_email = \'' . xtc_db_input($_POST['receive_email']) . '\',
						order_by = \'' . $order_by . '\'
					WHERE
						staff_id = \'' . xtc_db_input($_GET['sId']) . '\'
					';
			}
			xtc_db_query($sql);
		}
		break;
	
	case 'delete':
		if(isset($_POST['delete']))
		{
			$sql = 'DELETE FROM ' . TABLE_HELPDESK_STAFF . ' WHERE staff_id = \'' . xtc_db_input($_GET['sId']) . '\'';
			xtc_db_query($sql);
			xtc_redirect(xtc_href_link($filename, xtc_get_all_get_params( array('sId', 'action') ) ));
		}
		break;
	
	

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
	require_once 'helpdesk_menu.php';
	
	
	switch($_GET['action'])
	{
		default:
		case 'list':
			echo '<form action="'.xtc_href_link($filename, xtc_get_all_get_params(array('dId', 'action')).'&action=save_sort').'" method="POST">';
			echo '<table class="listing">';
				echo '<tr>';
					echo '<th>' . HELPDESK_TEXT_STAFF_FIRSTNAME . '</th>';
					echo '<th>' . HELPDESK_TEXT_STAFF_LASTNAME . '</th>';
					echo '<th>' . BUTTON_EDIT . '</th>';
					echo '<th>' . BUTTON_DELETE . '</th>';
				echo '</tr>';
			
			$sql = 'SELECT * FROM ' . TABLE_HELPDESK_STAFF . ' AS s, ' . TABLE_CUSTOMERS . ' AS c WHERE s.customers_id = c.customers_id ORDER BY customers_lastname, customers_firstname';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				echo '<tr  style="background-color:#'.$row['priority_color'].';">';
					echo '<td>'.$row['customers_firstname'].'</td>';
					echo '<td>'.$row['customers_lastname'].'</td>';
					echo '<td><a href="'.xtc_href_link($filename, xtc_get_all_get_params(array('sId', 'action')).'&action=edit&sId='.$row['staff_id'] ) . '">'.BUTTON_EDIT.'</a></td>';
					echo '<td><a href="'.xtc_href_link($filename, xtc_get_all_get_params(array('sId', 'action')).'&action=delete&sId='.$row['staff_id'] ) . '">'.BUTTON_DELETE.'</a></td>';
				echo '</tr>';
			}
			
			echo '</table>';
			echo '<br />';
			echo '<a href="'.xtc_href_link($filename, xtc_get_all_get_params(array('sId', 'action')).'&action=edit&sId=new' ) . '">'.BUTTON_NEW_STAFF.'</a>';
			echo '</form>';
			break;
		
		case 'delete':
			echo '<form action="'.xtc_href_link($filename, xtc_get_all_get_params(array('action')).'&action=delete').'" method="POST">';
			
			echo HELPDESK_TEXT_STAFF_DELETE_CONFIRM;
			
			echo '<br />';
			echo '<br />';
			
			echo '<input type="submit" name="cancel" value="'.BUTTON_CANCEL.'" />&nbsp;&nbsp;';
			echo '<input type="submit" name="delete" value="'.BUTTON_DELETE.'" />';
			
			echo '</form>';
			break;
		
		case 'edit':
			echo '<form action="'.xtc_href_link($filename, xtc_get_all_get_params(array('action')).'&action=save').'" method="POST">';
			
			if($_GET['sId'] == 'new')
			{
				$staff = array(
						'departments' => 'all',
						'languages' => 'all',
						'receive_email' => '1',
						'order_by' => 'status',
						'customers_id' => 1
					);
			}
			else
			{
				$sql = 'SELECT * FROM ' . TABLE_HELPDESK_STAFF . ' AS s, ' . TABLE_CUSTOMERS . ' AS c WHERE s.customers_id = c.customers_id AND staff_id = \''.xtc_db_input($_GET['sId']) . '\'';
				$rs = xtc_db_query($sql);
				$staff = xtc_db_fetch_array($rs);
			}
			
			$sql = 'SELECT * FROM ' . TABLE_HELPDESK_STAFF . ' ';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				$allreadyStaff[$row['customers_id']] = true;
			}
						
			$sql = 'SELECT * FROM ' . TABLE_CUSTOMERS . ' WHERE customers_status = \'0\' ORDER BY customers_lastname, customers_firstname';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				if($allreadyStaff[$row['customers_id']] !== true || ($row['customers_id'] == $staff['customers_id'] && $_GET['sId'] != 'new'))
				{
					$adminDropDown[] = array('id' => $row['customers_id'], 'text' => $row['customers_firstname'] . ' ' . $row['customers_lastname']);
				}
			}
			
			echo '<table class="list">';
				echo '<tr>';
					echo '<th>';
						echo HELPDESK_TEXT_STAFF_EDIT_CUSTOMER.':';
					echo '</th>';
					echo '<td>';
						echo xtc_draw_pull_down_menu('customers_id', $adminDropDown, $staff['customers_id']);
					echo '</td>';
				echo '</tr>';
				
				echo '<tr>';
					echo '<th>';
						echo HELPDESK_TEXT_STAFF_EDIT_RECEIVE_EMAIL.':';
					echo '</th>';
					echo '<td>';
						echo '<input type="checkbox" name="receive_email" value="1"';
						if($staff['receive_email'] == '1')
							echo ' checked="checked" ';						
						echo ' />';
					echo '</td>';
				echo '</tr>';
				
				echo '<tr>';
					echo '<th>';
						echo HELPDESK_TEXT_STAFF_EDIT_ORDER_BY.':';
					echo '</th>';
					echo '<td>';
						$orderDropDown[] = array('id' => 'status', 'text' => HELPDESK_TEXT_STAFF_EDIT_STATUS);
						$orderDropDown[] = array('id' => 'priority', 'text' => HELPDESK_TEXT_STAFF_EDIT_PRIORITY);
						echo xtc_draw_pull_down_menu('order_by', $orderDropDown, $staff['order_by']);
					echo '</td>';
				echo '</tr>';
				
				
				echo '<tr>';
						$deps = parseStringToArray($staff['departments']);
						
						$sql = 'SELECT * FROM ' . TABLE_HELPDESK_DEPARTMENTS . ' WHERE languages_id = '.(int)$_SESSION['languages_id'] . ' ORDER BY sort_order';
						$rs = xtc_db_query($sql);
					echo '<th>';
						echo HELPDESK_TEXT_STAFF_EDIT_DEPARTMENTS.':';
					echo '</th>';
					echo '<td>';
					echo '<table class="list">';
					while($row = xtc_db_fetch_array($rs))
					{
						echo '<tr>';
							echo '<td>';
								echo '<input type="checkbox" name="departments['.$row['department_id'].']" value="1" ';
								if($deps == 'all' || in_array($row['department_id'], $deps))
									echo ' checked="checked" ';
								echo ' />';
							echo '</td>';
							echo '<td>';
								echo $row['department_name'];
							echo '</td>';
						echo '</tr>';
					}
					echo '</table>';
					echo '</td>';
				echo '</tr>';
				
				
				
				echo '<tr>';
						$langs = parseStringToArray($staff['languages']);
						
						$sql = 'SELECT * FROM ' . TABLE_LANGUAGES . ' ORDER BY sort_order';
						$rs = xtc_db_query($sql);
					echo '<th>';
						echo HELPDESK_TEXT_STAFF_EDIT_LANGUAGES.':';
					echo '</th>';
					echo '<td>';
					echo '<table class="list">';
					while($row = xtc_db_fetch_array($rs))
					{
						echo '<tr>';
							echo '<td>';
								echo '<input type="checkbox" name="languages['.$row['languages_id'].']" value="1" ';
								if($langs == 'all' || in_array($row['languages_id'], $langs))
									echo ' checked="checked" ';
								echo ' />';
							echo '</td>';
							echo '<td>';
								echo $row['name'];
							echo '</td>';
						echo '</tr>';
					}
					echo '</table>';
					echo '</td>';
				echo '</tr>';
								
				
			echo '</table>';
			
			echo '<br />';
			echo '<input type="submit" name="cancel" value="'.BUTTON_CANCEL.'" />&nbsp;&nbsp;';
			echo '<input type="submit" name="save" value="'.BUTTON_SAVE.'" />';
			
			echo '</form>';
			break;
		
	}

?>
                </td>
            </tr>
         </table>
      </td>
   </tr>
</table>
</body>
</html>