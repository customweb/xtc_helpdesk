<?php
/**
::Header::
 */

$box_smarty = new smarty;
$box_content = '';

if(isset ($_SESSION['customer_id']))
	$box_content .= '<a href="' . xtc_href_link('helpdesk.php', '', 'SSL') . '">' . HELPDESK_MENU_OVERVIEW .  '</a><br />';

$box_content .= '<a href="' . xtc_href_link('helpdesk.php', 'action=new_ticket', 'SSL') . '">' . HELPDESK_MENU_NEW_TICKET .  '</a><br />';


$box_smarty->assign('BOX_CONTENT', $box_content);
$box_smarty->assign('BOX_HEADING', HEADING_HELPDESK);

// set cache ID
if (!$cache)
	$box_helpdesk = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_helpdesk.html');
else
	$box_helpdesk = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_helpdesk.html', $cache_id);

$smarty->assign('box_HELPDESK', $box_helpdesk);

