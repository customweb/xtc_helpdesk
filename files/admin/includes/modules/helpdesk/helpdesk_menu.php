<?php
/**
::Header::
 */

$menu[] = array
		(
			'name' => HELPDESK_TEXT_MENU_MY_HELPDESK,
			'href' => $filename.'?page=my_helpdesk'
		);

$menu[] = array
		(
			'name' => HELPDESK_TEXT_MENU_STAFF,
			'href' => $filename.'?page=staff'
		);

$menu[] = array
		(
			'name' => HELPDESK_TEXT_MENU_DEPARTMENTS,
			'href' => $filename.'?page=departments'
		);

$menu[] = array
		(
			'name' => HELPDESK_TEXT_MENU_STATUS,
			'href' => $filename.'?page=status'
		);

$menu[] = array
		(
			'name' => HELPDESK_TEXT_MENU_PRIORITIES,
			'href' => $filename.'?page=priorities'
		);

$menu[] = array
		(
			'name' => HELPDESK_TEXT_MENU_CONFIG,
			'href' => $filename.'?page=config'
		);

?>
<style type="text/css">
	.helpdesk_menu
	{
		float:left;
	}
	.helpdesk_menu a:link, .helpdesk_menu a:visited
	{
		border-style:solid;
		border-width:1px;
		border-color:#999999;
		padding: 3px 10px 3px 10px;
		margin: 1px 3px 1px 3px;
	}
	.helpdesk_menu a:hover, .helpdesk_menu a:focus
	{
		background-color:#FFFFFF;
	}
	
</style>
<?php
$i = 0;
foreach($menu as $data)
{
	echo '<div class="helpdesk_menu">';
		echo '<a href="' . $data['href'] . '" title="'.$data['name'].'">'.$data['name'].'</a>';
	echo '</div>';
	$i++;
}
echo '<br />';
echo '<br />';

?>
