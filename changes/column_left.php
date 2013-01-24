    /**
	 * Helpdesk - By customweb GmbH
	 */
    echo ('<div class="dataTableHeadingContent"><b>Helpdesk</b></div>');
    if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['my_helpdesk'] == '1')) echo '<a href="' . xtc_href_link('my_helpdesk.php', '', 'NONSSL') . '" class="menuBoxContentLink"> -My Helpdesk</a><br>';
    if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['helpdesk'] == '1')) echo '<a href="' . xtc_href_link('helpdesk.php', '', 'NONSSL') . '" class="menuBoxContentLink"> -Helpdesk</a><br>';
    /**
     * End Helpdesk
     */
