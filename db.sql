ALTER TABLE `admin_access` ADD `helpdesk` INT( 1 ) NOT NULL DEFAULT '0';
UPDATE `admin_access` SET `helpdesk` = '1' WHERE `admin_access`.`customers_id` = '1';
ALTER TABLE `admin_access` ADD `my_helpdesk` INT( 1 ) NOT NULL DEFAULT '0';
UPDATE `admin_access` SET `my_helpdesk` = '1' WHERE `admin_access`.`customers_id` = '1';




--
-- Tabellenstruktur für Tabelle `helpdesk_departments`
--

CREATE TABLE `helpdesk_departments` (
  `department_id` tinyint(3) unsigned NOT NULL,
  `languages_id` tinyint(3) unsigned NOT NULL default '1',
  `department_name` varchar(40) default NULL,
  `sort_order` int(11) NOT NULL,
  KEY `department_id` (`department_id`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `helpdesk_departments`
--

INSERT INTO `helpdesk_departments` (`department_id`, `languages_id`, `department_name`, `sort_order`) VALUES
(1, 1, 'Accounting', 2),
(1, 2, 'Buchhaltung', 2),
(2, 1, 'Products', 1),
(2, 2, 'Produkte', 1),
(3, 1, 'Warranty', 3),
(3, 2, 'Garantie', 3);


--
-- Tabellenstruktur für Tabelle `helpdesk_staff`
--

CREATE TABLE `helpdesk_staff` (
  `staff_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL,
  `departments` varchar(255) NOT NULL default 'all',
  `languages` varchar(255) NOT NULL default 'all',
  `receive_email` char(1) NOT NULL default '1',
  `order_by` enum('priority','status') NOT NULL default 'status',
  PRIMARY KEY  (`staff_id`)
) TYPE=MyISAM  AUTO_INCREMENT=3 ;


--
-- Tabellenstruktur für Tabelle `helpdesk_tickets`
--

CREATE TABLE `helpdesk_tickets` (
  `ticket_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) unsigned NOT NULL default '0',
  `customers_firstname` varchar(255) default NULL,
  `customers_lastname` varchar(255) default NULL,
  `customers_email` varchar(255) default NULL,
  `orders_id` int(11) NOT NULL default '0',
  `products_id` int(11) NOT NULL default '0',
  `languages_id` int(11) NOT NULL,
  `ticket_date` datetime NOT NULL,
  PRIMARY KEY  (`ticket_id`),
  KEY `customers_id` (`customers_id`)
) TYPE=MyISAM  AUTO_INCREMENT=1 ;



--
-- Tabellenstruktur für Tabelle `helpdesk_tickets_history`
--

CREATE TABLE `helpdesk_tickets_history` (
  `history_id` int(11) NOT NULL auto_increment,
  `ticket_id` int(11) unsigned NOT NULL default '0',
  `status_id` tinyint(1) unsigned default NULL,
  `last_modified` datetime default NULL,
  `department_id` tinyint(3) unsigned default NULL,
  `customers_id` tinyint(3) unsigned default NULL,
  `priority_id` tinyint(1) unsigned default NULL,
  `comment` text,
  `add_by` enum('supporter','customer') NOT NULL default 'supporter',
  `internal` char(1) NOT NULL default '0',
  PRIMARY KEY  (`history_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `ticket_status` (`status_id`)
) TYPE=MyISAM  AUTO_INCREMENT=1 ;


--
-- Tabellenstruktur für Tabelle `helpdesk_tickets_priority`
--

CREATE TABLE `helpdesk_tickets_priority` (
  `priority_id` smallint(11) unsigned NOT NULL,
  `languages_id` tinyint(3) unsigned NOT NULL default '1',
  `priority_name` varchar(40) default NULL,
  `sort_order` int(11) NOT NULL,
  `priority_color` varchar(6) NOT NULL default 'FFFFFF',
  PRIMARY KEY  (`priority_id`,`languages_id`),
  KEY `priority_id` (`priority_id`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `helpdesk_tickets_priority`
--

INSERT INTO `helpdesk_tickets_priority` (`priority_id`, `languages_id`, `priority_name`, `sort_order`, `priority_color`) VALUES
(1, 1, 'High', 3, 'FCDAC2'),
(1, 2, 'Hoch', 3, 'FCDAC2'),
(2, 1, 'Normal', 2, 'FBFCBC'),
(2, 2, 'Normal', 2, 'FBFCBC'),
(3, 1, 'Low', 1, 'D9EFFF'),
(3, 2, 'Niedrig', 1, 'D9EFFF');


--
-- Tabellenstruktur für Tabelle `helpdesk_tickets_status`
--

CREATE TABLE `helpdesk_tickets_status` (
  `status_id` tinyint(3) unsigned NOT NULL,
  `languages_id` tinyint(3) unsigned NOT NULL default '1',
  `status_name` varchar(25) default NULL,
  `sort_order` int(11) NOT NULL,
  `status_color` varchar(6) NOT NULL default 'FFFFFF',
  PRIMARY KEY  (`status_id`,`languages_id`),
  KEY `status_id` (`status_id`)
) TYPE=MyISAM ;

--
-- Daten für Tabelle `helpdesk_tickets_status`
--

INSERT INTO `helpdesk_tickets_status` (`status_id`, `languages_id`, `status_name`, `sort_order`, `status_color`) VALUES
(1, 1, 'Open', 1, 'F78891'),
(1, 2, 'Offen', 1, 'F78891'),
(2, 1, 'Closed', 2, 'CEFDD5'),
(2, 2, 'Geschlossen', 2, 'CEFDD5');


-- 
-- Configuration
--
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_DEFAULT_STATUS', '1', 175, 1, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_DEFAULT_CLOSED_STATUS', '2', 175, 5, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_DEFAULT_PRIORITY', '2', 175, 10, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_NEW_TICKET_LOGIN', 'False', 175, 15, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_CAPTCHA', 'NoWhenLogin', 175, 20, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''Yes'', ''No'', ''NoWhenLogin''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_QUESTION_PRODUCT_DEFAULT_DEPARTMENT', '2', 175, 25, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_QUESTION_ORDER_DEFAULT_DEPARTMENT', '1', 175, 30, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_FROM_ADDRESS', 'helpdesk@your-domain.com', 175, 35, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_FROM_NAME', 'Helpdesk', 175, 40, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_REPLY_ADDRESS', 'helpdesk@your-domain.com', 175, 45, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_REPLY_NAME', 'Helpdesk', 175, 50, NULL, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_TICKET', 'True', 175, 55, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_STAFF_NEW_TICKET', 'True', 175, 60, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_ANSWER', 'True', 175, 65, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_EMAIL_STAFF_NEW_ANSWER', 'True', 175, 70, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_MAKE_AUTO_COMMENTS', 'True', 175, 75, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES('HELPDESK_TICKETS_CUSTOMER_SET_PRIORITY', 'False', 175, 80, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
