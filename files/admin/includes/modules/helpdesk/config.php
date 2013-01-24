<?php
/**
::Header::
 */

if($filename != 'helpdesk.php')
{
	xtc_redirect(FILENAME_MY_HELPDESK);
}

if ($_GET['action'])
{
	switch ($_GET['action'])
	{
		case 'save':
			$configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '175' order by sort_order");

			while ($configuration = xtc_db_fetch_array($configuration_query))
				xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");
			
			xtc_redirect(xtc_href_link($filename, xtc_get_all_get_params(array('action'))));
			break;
			
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
	require_once 'helpdesk_menu.php';

	echo '<form action="'.xtc_href_link($filename, xtc_get_all_get_params(array('action')).'&action=save').'" method="POST">';
	echo '<table width="100%"  border="0" cellspacing="0" cellpadding="4">';
	
	$configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '175' order by sort_order");
	
	while ($configuration = xtc_db_fetch_array($configuration_query))
	{
		if (xtc_not_null($configuration['use_function']))
		{
			$use_function = $configuration['use_function'];
			if (ereg('->', $use_function))
			{
				$class_method = explode('->', $use_function);
				if (!is_object(${$class_method[0]}))
				{
					include(DIR_WS_CLASSES . $class_method[0] . '.php');
					${$class_method[0]} = new $class_method[0]();
				}
				$cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
			}
			else
			{
				$cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
			}
		}
		else
		{
			$cfgValue = $configuration['configuration_value'];
		}
	
		if ($configuration['set_function'])
		{
			eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
		}
		else
		{
			$value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=40');
		}
		// add
	
		if (strstr($value_field,'configuration_value'))
			$value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
	
		echo '<tr>';
		echo '<td width="300" valign="top" class="dataTableContent"><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b></td>';
		echo '<td valign="top" class="dataTableContent">';
		echo '<table width="100%"  border="0" cellspacing="0" cellpadding="2">';
		echo '<tr>';
		echo '<td style="background-color:#FCF2CF ; border: 1px solid; border-color: #CCCCCC;" class="dataTableContent">'.$value_field.'</td>';
		echo '</tr>';
		echo '</table>';
		echo '<br />'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>';
		echo '</tr>';
	
	}
	
	echo '</table>';
	echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
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