<?php
/**
::Header::
 */

if($filename != 'helpdesk.php')
{
	xtc_redirect(FILENAME_MY_HELPDESK);
}
$config = array(
			'primary_field' => 'status_id',
			'id' => $_GET['dId'],
			'table' => TABLE_HELPDESK_TICKETS_STATUS,
			'prefix' => 'status',
			'language_field'=> 'languages_id',
			'fields'		=> array(
									'status_name' 	=> array('type' => 'varchar', 'multilingual' => true,  'lable' => HELPDESK_TEXT_STATUS_NAME),
									'status_color' 	=> array('type' => 'varchar', 'multilingual' => false,  'lable' => HELPDESK_TEXT_STATUS_COLOR),
									'sort_order' 	=> array('type' => 'integer', 'multilingual' => false, 'lable' => HELPDESK_TEXT_STATUS_SORT_ORDER)
								)
		);

if(isset($_POST['cancel']))
{
	xtc_redirect(xtc_href_link($filename, xtc_get_all_get_params( array('dId', 'action') ) ));
}

switch($_GET['action'])
{
	case 'save_sort':
		if(isset($_POST['sort_order']))
		{
			foreach($_POST['sort_order'] as $id => $value)
			{
				$sql = 'UPDATE '.TABLE_HELPDESK_TICKETS_STATUS	.' SET sort_order = \'' . xtc_db_input($value) . '\' WHERE status_id = \'' . xtc_db_input($id) . '\'';
				xtc_db_query($sql);
			}
		}
		break;
	
	case 'save':
		if(isset($_POST['save']))
		{
			saveMultiLingualContent($config, $_POST['status']);
		}
		break;
	
	case 'delete':
		if(isset($_POST['delete']))
		{
			$sql = 'DELETE FROM ' . TABLE_HELPDESK_TICKETS_STATUS . ' WHERE status_id = \'' . xtc_db_input($_GET['dId']) . '\'';
			xtc_db_query($sql);
			xtc_redirect(xtc_href_link($filename, xtc_get_all_get_params( array('dId', 'action') ) ));
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
<script type="text/javascript" src="includes/modules/helpdesk/js/prototype.js"></script>
<script type="text/javascript" src="includes/modules/helpdesk/js/tabs.js"></script>
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
                            <td class="pageHeading">Helpdesk</td>
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
					echo '<th>' . HELPDESK_TEXT_STATUS_NAME . '</th>';
					echo '<th>' . HELPDESK_TEXT_STATUS_SORT_ORDER . ' <input type="submit" name="save_sort" value="'.BUTTON_SAVE.'" /></th>';
					echo '<th>' . HELPDESK_TEXT_STATUS_COLOR . '</th>';
					echo '<th>' . BUTTON_EDIT . '</th>';
					echo '<th>' . BUTTON_DELETE . '</th>';
				echo '</tr>';
			
			$sql = 'SELECT * FROM ' . TABLE_HELPDESK_TICKETS_STATUS . ' WHERE languages_id = \'' . (int)$_SESSION['languages_id'] . '\' ORDER BY sort_order';
			$rs = xtc_db_query($sql);
			while($row = xtc_db_fetch_array($rs))
			{
				echo '<tr style="background-color:#'.$row['status_color'].';">';
					echo '<td>'.$row['status_name'].'</td>';
					echo '<td><input type="text" name="sort_order['.$row['status_id'].']" style="text-align:center;" size="3" value="'.$row['sort_order'].'" /></td>';
					echo '<td>'.$row['status_color'].'</td>';
					echo '<td><a href="'.xtc_href_link($filename, xtc_get_all_get_params(array('dId', 'action')).'&action=edit&dId='.$row['status_id'] ) . '">'.BUTTON_EDIT.'</a></td>';
					echo '<td><a href="'.xtc_href_link($filename, xtc_get_all_get_params(array('dId', 'action')).'&action=delete&dId='.$row['status_id'] ) . '">'.BUTTON_DELETE.'</a></td>';
				echo '</tr>';
			}
			
			echo '</table>';
			echo '<br />';
			echo '<a href="'.xtc_href_link($filename, xtc_get_all_get_params(array('dId', 'action')).'&action=edit&dId=new' ) . '">'.BUTTON_NEW_STATUS.'</a>';
			echo '</form>';
			break;
		
		case 'delete':
			echo '<form action="'.xtc_href_link($filename, xtc_get_all_get_params(array('action')).'&action=delete').'" method="POST">';
			
			echo HELPDESK_TEXT_STATUS_DELETE_CONFIRM;
			
			echo '<br />';
			echo '<br />';
			
			echo '<input type="submit" name="cancel" value="'.BUTTON_CANCEL.'" />&nbsp;&nbsp;';
			echo '<input type="submit" name="delete" value="'.BUTTON_DELETE.'" />';
			
			echo '</form>';
			break;
		
		case 'edit':
			echo '<form action="'.xtc_href_link($filename, xtc_get_all_get_params(array('action')).'&action=save').'" method="POST">';
			
			
			echo multiLingualTabs($config);
			
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