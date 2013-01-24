<?php
/**
::Header::
 */
 
ob_start();
require('includes/application_top.php');


// add some defines:
define('FILENAME_MY_HELPDESK', 'my_helpdesk.php');
define('FILENAME_HELPDESK', 'helpdesk.php');
define('TABLE_HELPDESK_DEPARTMENTS', 'helpdesk_departments');
define('TABLE_HELPDESK_TICKETS', 'helpdesk_tickets');
define('TABLE_HELPDESK_TICKETS_HISTORY', 'helpdesk_tickets_history');
define('TABLE_HELPDESK_TICKETS_PRIORITY', 'helpdesk_tickets_priority');
define('TABLE_HELPDESK_TICKETS_STATUS', 'helpdesk_tickets_status');
define('TABLE_HELPDESK_STAFF', 'helpdesk_staff');

if(isset($_SERVER['SCRIPT_FILENAME']))
{
	$filename = basename($_SERVER['SCRIPT_FILENAME']);
}
elseif(isset($_SERVER['SCRIPT_NAME']))
{
	$filename = basename($_SERVER['SCRIPT_NAME']);
}

switch($_GET['page'])
{
	default:
	case 'my_helpdesk':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/my_helpdesk.php';
		break;
	
	case 'edit_ticket':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/edit_ticket.php';
		break;
	
	case 'staff':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/staff.php';
		break;
	
	case 'departments':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/departments.php';
		break;
	
	case 'status':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/status.php';
		break;
	
	case 'priorities':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/priorities.php';
		break;
	
	case 'config':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'helpdesk/config.php';
		break;
}


function parseStringToArray($string)
{
	if($string == 'all')
	{
		return $string;
	}
	else
	{
		$ex = explode(',', $string);
		if(!is_array($ex))
			return array($ex);
		else
			return $ex;
	}
}

function cmpPrioritySort($a, $b)
{
   	if($a['priority_sort'] > $b['priority_sort'])
	{
		return 1;
	}
	elseif($a['priority_sort'] < $b['priority_sort'])
	{
		return -1;
	}
	elseif($a['priority_sort'] == $b['priority_sort'] && $a['last_modified'] > $b['last_modified'])
	{
        return 1;
    }
	elseif($a['priority_sort'] == $b['priority_sort'] && $a['last_modified'] < $b['last_modified'])
	{
		return -1;
	}
	else
	{
		return 0;
	}
}


function cmpStatusSort($a, $b)
{
    if($a['status_sort'] > $b['status_sort'])
	{
		return -1;
	}
	elseif($a['status_sort'] < $b['status_sort'])
	{
		return 1;
	}
	elseif($a['status_sort'] == $b['status_sort'] && $a['last_modified'] > $b['last_modified'])
	{
        return 1;
    }
	elseif($a['status_sort'] == $b['status_sort'] && $a['last_modified'] < $b['last_modified'])
	{
		return -1;
	}
	else
	{
		return 0;
	}
}


function mergeColor($first, $second)
{
	$decFirst = array(
					hexdec(substr($first, 0, 2)),
					hexdec(substr($first, 2, 2)),
					hexdec(substr($first, 4, 2))
					);
	$decSecond = array(
					hexdec(substr($second, 0, 2)),
					hexdec(substr($second, 2, 2)),
					hexdec(substr($second, 4, 2))
					);
	$decColor = array(
					($decFirst[0]+$decSecond[0])/2,
					($decFirst[1]+$decSecond[1])/2,
					($decFirst[2]+$decSecond[2])/2,
					);
	return dechex($decColor[0]).dechex($decColor[1]). dechex($decColor[2]);
}

function saveMultiLingualContent($config, $data)
{
	foreach($data as $langId => $fields)
	{
		if($config['id'] != 'new')
		{
			$sql = 'SELECT * FROM ' .$config['table'] . ' WHERE ' . $config['primary_field'] . ' = \'' . xtc_db_input($config['id']) . '\' AND ' . $config['language_field'] . ' = \'' . xtc_db_input($langId) . '\'';
			$rs = xtc_db_query($sql);
			if(xtc_db_num_rows($rs) > 0)
			{
				$update = true;
			}
			else
			{
				$update = false;
			}
		}
		
		if($update)
		{
			$fieldSql = '';
			foreach($fields as $name => $value)
			{
				$fieldSql .= $name.' = \'' . xtc_db_input($value) . '\', '; 
			}
			if($fieldSql != '')
			{
				$fieldSql = substr($fieldSql, 0, -2);
				$sql = 'UPDATE ' . $config['table'] . ' SET 
							' . $fieldSql . ' 
						WHERE 
							' . $config['language_field'] . ' = \'' . xtc_db_input($langId) . '\' 
						   AND
							' . $config['primary_field'] . ' = \'' . xtc_db_input($config['id']) . '\'';
			}
			
		}
		else
		{
			// Get next id:
			if($config['id'] == 'new')
			{
				$sql = 'SELECT max('.$config['primary_field'].') AS max FROM ' . $config['table'] . '';
				$rs = xtc_db_query($sql);
				if($row = xtc_db_fetch_array($rs))
					$id = $row['max'] + 1;
				else
					$id = 1;
			}
			else
			{
				$id = $config['id'];
			}
			
			$fieldSql = '';
			$valueSql = '';
			foreach($fields as $name => $value)
			{
				$fieldSql .= $name.', '; 
				$valueSql .= '\'' . xtc_db_input($value) . '\', '; 
			}
			
			if($fieldSql != '')
			{
				$sql = 'INSERT INTO ' . $config['table'] . ' 
							(' . $fieldSql . ' ' . $config['language_field'] . ', ' . $config['primary_field'] . ')
						VALUES
							(' . $valueSql . ' ' . xtc_db_input($langId) . ', ' . $id . ')
						';
				$config['id'] = $id;
			}
		}
		xtc_db_query($sql);
	}
	return $config['id'];
}


function multiLingualTabs($config)
{
	$cookieName = 'tabs';
	$cookie = $cookieName.'['.$config['prefix'] .']';
	
	if(isset($_GET[$cookieName][$config['prefix']]))
	{
		setcookie($cookie, $_GET[$cookieName][$config['prefix']]);
		$_COOKIE[$cookieName][$config['prefix']] = $_GET[$cookieName][$config['prefix']];
	}
	
	if(!isset($_COOKIE[$cookieName][$config['prefix']]))
	{
		setcookie($cookie, $_SESSION['language_code']);
		$_COOKIE[$cookieName][$config['prefix']] = $_SESSION['language_code'];
	}
		

	if(isset($_SERVER['SCRIPT_FILENAME']))
	{
		$filename = basename($_SERVER['SCRIPT_FILENAME']);
	}
	elseif(isset($_SERVER['SCRIPT_NAME']))
	{
		$filename = basename($_SERVER['SCRIPT_NAME']);
	}
	
	if($config['id'] != 'new')
	{
		$sql = 'SELECT * FROM ' .$config['table'] . ' WHERE ' . $config['primary_field'] . ' = \'' . xtc_db_input($config['id']) . '\'';
		$rs = xtc_db_query($sql);
		while($row = xtc_db_fetch_array($rs))
		{
			$data[$row[ $config['language_field'] ]] = $row;
		}
	}
	
	$langSql = 'SELECT * FROM ' . TABLE_LANGUAGES . ' ORDER BY sort_order';
	$rs = xtc_db_query($langSql);
	while($l = xtc_db_fetch_array($rs))
	{
		$langs[$l['languages_id']] = $l;
	}
	
	$output = '<div class="Tab" id="tab_'.$config['prefix'].'">';
	
	// Make Tabs:
	$output .= '<ul>';
	foreach($langs as $id => $langData)
	{
		$output .= '<li><a class="';
		if($_COOKIE[$cookieName][$config['prefix']] == $langData['code'])
			$output .= ' Active';
		$output .= '" id="'.$config['prefix'].'_'.$langData['code'] . '_link" href="'.xtc_href_link($filename, xtc_get_all_get_params(array('tabs')) ).'&tabs['.$config['prefix'].']='.$langData['code'] . '">'.$langData['name'].'</a></li>';
	}
	$output .= '</ul>';
	
	@$noMultilingualData = current($data);
	
	foreach($langs as $id => $langData)
	{
		$output .= '<div class="Content';
		if($_COOKIE[$cookieName][$config['prefix']] == $langData['code'])
			$output .= ' Active';
		$output .= '" id="'.$config['prefix'].'_'.$langData['code'] . '_content"><table>';
		foreach($config['fields'] as $name => $option)
		{
			switch($option['type'])
			{
				case 'varchar':
					$output .= '<tr><th><label for="'.$config['prefix'].'_'.$name.'_'.$id.'">'.$option['lable'].':</label></td>';
					$output .= '<td><input id="'.$config['prefix'].'_'.$name.'_'.$id.'" type="text" name="'.$config['prefix'].'['.$id.']['.$name.']" value="'.$data[$id][$name].'" /></td></tr>';
					break;
				case 'integer':
					$output .= '<tr><th><label for="'.$config['prefix'].'_'.$name.'_'.$id.'">'.$option['lable'].':</label></td>';
					$output .= '<td><input id="'.$config['prefix'].'_'.$name.'_'.$id.'" type="text" name="'.$config['prefix'].'['.$id.']['.$name.']" value="'.$data[$id][$name].'" size="5" /></td></tr>';
					break;
			}
		}
		$output .= '</table></div>';
	}
	
	$output .= '</div>';
	
	return $output;
}

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

require(DIR_WS_INCLUDES . 'application_bottom.php');


?>