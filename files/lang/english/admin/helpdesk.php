<?php
/**
::Header::
 */
 
/**
 * Menu
 */
define('HELPDESK_TEXT_MENU_MY_HELPDESK', 'My Helpdesk');
define('HELPDESK_TEXT_MENU_STAFF', 'Staff');
define('HELPDESK_TEXT_MENU_DEPARTMENTS', 'Departments');
define('HELPDESK_TEXT_MENU_STATUS', 'Status');
define('HELPDESK_TEXT_MENU_PRIORITIES', 'Priority');
define('HELPDESK_TEXT_MENU_CONFIG', 'Configurations');


/**
 * Staff
 */
define('HELPDESK_TEXT_STAFF_FIRSTNAME', 'Firstname');
define('HELPDESK_TEXT_STAFF_LASTNAME', 'Lastname');
define('BUTTON_NEW_STAFF', 'New staff');
define('HELPDESK_TEXT_STAFF_EDIT_CUSTOMER', 'Customer');
define('HELPDESK_TEXT_STAFF_EDIT_RECEIVE_EMAIL', 'Send e-mail');
define('HELPDESK_TEXT_STAFF_EDIT_ORDER_BY', 'Order');
define('HELPDESK_TEXT_STAFF_EDIT_STATUS', 'Status');
define('HELPDESK_TEXT_STAFF_EDIT_PRIORITY', 'Priority');
define('HELPDESK_TEXT_STAFF_EDIT_DEPARTMENTS', 'Departments');
define('HELPDESK_TEXT_STAFF_EDIT_LANGUAGES', 'Languages');
define('HELPDESK_TEXT_STAFF_DELETE_CONFIRM', 'Do you want to delete this staff?');


/**
 * Departments
 */
define('HELPDESK_TEXT_DEPARTMENTS_NAME', 'Name');
define('HELPDESK_TEXT_DEPARTMENTS_SORT_ORDER', 'Order');
define('HELPDESK_TEXT_DEPARTMENTS_DELETE_CONFIRM', 'Do you want to delete this departmetn');
define('BUTTON_NEW_DEPARTMENT', 'New deparmtment');


/**
 * Status
 */
define('HELPDESK_TEXT_STATUS_NAME', 'Name');
define('HELPDESK_TEXT_STATUS_SORT_ORDER', 'Order');
define('HELPDESK_TEXT_STATUS_COLOR', 'Color');
define('HELPDESK_TEXT_STATUS_DELETE_CONFIRM', 'Wollen Sie diesen Status wirklich l&ouml;schen?');
define('BUTTON_NEW_STATUS', 'Neuer Status anlegen');


/**
 * Priority
 */
define('HELPDESK_TEXT_PRIORITY_NAME', 'Name');
define('HELPDESK_TEXT_PRIORITY_SORT_ORDER', 'Sortierung');
define('HELPDESK_TEXT_PRIORITY_COLOR', 'Farbe');
define('HELPDESK_TEXT_PRIORITY_DELETE_CONFIRM', 'Wollen Sie diese Priorit&auml;t wirklich l&ouml;schen?');
define('BUTTON_NEW_PRIORITY', 'Neue Priorit&auml;t anlegen');


/**
 * Config
 */
define('HELPDESK_TICKETS_DEFAULT_STATUS_TITLE', 'Status f&uuml;r neue Tickets');
define('HELPDESK_TICKETS_DEFAULT_STATUS_DESC', 'Gegen Sie hier den Status an der f&uuml;r neue Tickets verwendet werden soll.');

define('HELPDESK_TICKETS_DEFAULT_CLOSED_STATUS_TITLE', 'Status f&uuml;r geschlossene Tickets');
define('HELPDESK_TICKETS_DEFAULT_CLOSED_STATUS_DESC', 'Gegen Sie hier den Status an der f&uuml;r geschlossene Tickets verwendet werden soll.');

define('HELPDESK_TICKETS_DEFAULT_PRIORITY_TITLE', 'Standard Priorit&auml;t');
define('HELPDESK_TICKETS_DEFAULT_PRIORITY_DESC', 'Geben Sie hier die standard Priorit&auml;t an.');

define('HELPDESK_TICKETS_NEW_TICKET_LOGIN_TITLE', 'Login beim Erstellen der Tickets');
define('HELPDESK_TICKETS_NEW_TICKET_LOGIN_DESC', 'Sollen sich die Kunden einloggen m&uuml;ssen um ein neues Ticket anzulegen?');

define('HELPDESK_TICKETS_CAPTCHA_TITLE', 'Captcha');
define('HELPDESK_TICKETS_CAPTCHA_DESC', 'Soll eine Captcha verwendet werden? (NoWhenLogin = Ein Captcha wird nur angezeigt, wenn sich der Kunde nicht eingeloggt hat.)');

define('HELPDESK_TICKETS_QUESTION_PRODUCT_DEFAULT_DEPARTMENT_TITLE', 'Produkte Fragen - Abteilung');
define('HELPDESK_TICKETS_QUESTION_PRODUCT_DEFAULT_DEPARTMENT_DESC', 'In welche Abteilung sollen Fragen / Tickets zu Produkten angelegt werden? (leer = kein Zwang f&uuml;r Kunde)');

define('HELPDESK_TICKETS_QUESTION_ORDER_DEFAULT_DEPARTMENT_TITLE', 'Bestellung Tickets - Abteilung');
define('HELPDESK_TICKETS_QUESTION_ORDER_DEFAULT_DEPARTMENT_DESC', 'In welche Abteilung sollen Fragen / Tickets zu Bestellungen angelegt werden? (leer = kein Zwang f&uuml;r Kunde)');

define('HELPDESK_TICKETS_EMAIL_FROM_ADDRESS_TITLE', 'Von E-Mail: Adresse');
define('HELPDESK_TICKETS_EMAIL_FROM_ADDRESS_DESC', 'Geben Sie hier die E-Mail Adresse an von der E-Mail Nachrichten verschickt werden. (E-Mail Adresse sollte ein Autoreply haben.)');

define('HELPDESK_TICKETS_EMAIL_FROM_NAME_TITLE', 'Von E-Mail: Name');
define('HELPDESK_TICKETS_EMAIL_FROM_NAME_DESC', 'Geben Sie hier den Name ein der bei den E-Mail angezeigt werden soll. (Bsp. "Your Company - Helpdesk")');

define('HELPDESK_TICKETS_EMAIL_REPLY_ADDRESS_TITLE', 'Antwort E-Mail: Adresse');
define('HELPDESK_TICKETS_EMAIL_REPLY_ADDRESS_DESC', 'Geben Sie hier die E-Mail Adresse an, auf die geantwortet werden soll. (E-Mail Adresse sollte ein Autoreply haben.)');

define('HELPDESK_TICKETS_EMAIL_REPLY_NAME_TITLE', 'Antwort E-Mail: Name');
define('HELPDESK_TICKETS_EMAIL_REPLY_NAME_DESC', 'Geben Sie hier den Name ein der bei den E-Mail angezeigt werden soll. (Bsp. "Your Company - Helpdesk")');

define('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_TICKET_TITLE', 'E-Mail an Kunden: Neues Ticket');
define('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_TICKET_DESC', 'Soll eine E-Mail an den Kunden versendet werden, wenn er ein Ticket angelegt hat?');

define('HELPDESK_TICKETS_EMAIL_STAFF_NEW_TICKET_TITLE', 'E-Mail an Supporter: Neues Ticket');
define('HELPDESK_TICKETS_EMAIL_STAFF_NEW_TICKET_DESC', 'Sollen die Supporter (nur betroffene) per E-Mail informiert werden, dass ein neues Ticket vorhanden ist?');

define('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_ANSWER_TITLE', 'E-Mail an Kunden: Antwort');
define('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_ANSWER_DESC', 'Soll der Kunde informiert werden, wenn eine Antwort zu seinem Ticket erstellt wurde?');

define('HELPDESK_TICKETS_EMAIL_STAFF_NEW_ANSWER_TITLE', 'E-Mail an Supporter: Antwort');
define('HELPDESK_TICKETS_EMAIL_STAFF_NEW_ANSWER_DESC', 'Sollen die betroffenen Supporter per E-Mail informiert werden, wenn der Kunde eine Antwort auf sein Ticket gegeben hat?');

define('HELPDESK_TICKETS_MAKE_AUTO_COMMENTS_TITLE', 'Autokommentare');
define('HELPDESK_TICKETS_MAKE_AUTO_COMMENTS_DESC', 'Sollen automatisch Kommentare erstellt werden, wenn zum Beispiel ein Ticket in eine andere Abteilung verschoben wurde?');

define('HELPDESK_TICKETS_CUSTOMER_SET_PRIORITY_TITLE', 'Kunde kann Priorit&auml;t festlegen');
define('HELPDESK_TICKETS_CUSTOMER_SET_PRIORITY_DESC', 'Soll der Kunde die Priorit&auml;t festlegen k&ouml;nnen?');

/**
 * My Helpdesk
 */
define('HELPTESK_HEADING_TICKET_ID', '#');
define('HELPTESK_HEADING_TICKET_STATUS', 'Status');
define('HELPTESK_HEADING_TICKET_PRIORITY', 'Priorit&auml;t');
define('HELPTESK_HEADING_TICKET_DEPARTMENT', 'Abteilung');
define('HELPTESK_HEADING_TICKET_CREATE_DATE', 'Erstellungsdatum');
define('HELPTESK_HEADING_TICKET_LAST_MODIFIED', 'Letzte &Auml;nderung');
define('HELPTESK_HEADING_TICKET_CUSTOMER', 'Kunde');
define('HELPTESK_HEADING_TICKET_EDIT', 'Bearbeiten');

define('HELPDESK_TEXT_VIEW_TICKET', 'Geschrieben von <strong>%s:</strong>');
define('HELPDESK_TEXT_VIEW_TICKET_ID', 'Ticket #');
define('HELPDESK_TEXT_VIEW_TICKET_CUSTOMER', 'Kunde');
define('HELPDESK_TEXT_VIEW_TICKET_ORDER_ID', 'Bestellnr');
define('HELPDESK_TEXT_VIEW_TICKET_ORDER_ID_VIEW', 'Bestellung ansehen');
define('HELPDESK_TEXT_VIEW_TICKET_PRODUCT_ID', 'Artikel ID');
define('HELPDESK_TEXT_VIEW_TICKET_PRODUCT_ID_VIEW', 'Artikel ansehen');
define('HELPDESK_TEXT_VIEW_TICKET_DEPARTMENT', 'Abteilung');
define('HELPDESK_TEXT_VIEW_TICKET_PRIORITY', 'Priorit&auml;t');
define('HELPDESK_TEXT_VIEW_TICKET_COMMENT', 'Kommentar der &Auml;nderung');

define('HELPDESK_TEXT_VIEW_TICKET_ANSWER', 'Antwort');
define('HELPDESK_TEXT_VIEW_TICKET_INTERNAL', 'Intern');
define('HELPDESK_TEXT_ENTER_HERE_QUESTION', 'Geben Sie hier Ihre Antwort ein.');
define('HELPDESK_TEXT_NO_ORDER_CONNECTED', '- Keine Bestellung zugeordnet -');

define('TEXT_HELPDESK_ASC', 'Aufsteigend');
define('TEXT_HELPDESK_DESC', 'Absteigend');

define('TEXT_HELPDESK_SORT_BY_STATUS', 'Nach Status sortieren');
define('TEXT_HELPDESK_SORT_BY_PRIORITY', 'Nach Priorit&auml;t sortieren');

define('BUTTON_VIEW', 'Anzeigen');

define('TEXT_HELPDESK_TEXT_SITE_DROPDOWN', 'Seite %s');


define('HELPDESK_TEXT_AUTO_COMMENT', '<b>Automatischer Kommentar:</b>');

define('HELPDESK_TEXT_CUSTOMER_ID_CHANGED', 'Kunde ge&auml;ndert von <b>\'%1$s\'</b> nach <b>\'%2$s\'</b>.');
define('HELPDESK_TEXT_CUSTOMER_ID_ADDED', 'Kunde <b>\'%1$s\'</b> wurde hinzugef&uuml;gt.');

define('HELPDESK_TEXT_ORDERS_ID_CHANGED', 'Bestellnummer ge&auml;ndert von <b>%1$s</b> nach <b>%2$s</b>.');
define('HELPDESK_TEXT_ORDERS_ID_ADDED', 'Bestellnummer <b>%1$s</b> hinzugef&uuml;gt.');
define('HELPDESK_TEXT_ORDERS_ID_DELETED', 'Bestellnummer <b>%1$s</b> gel&ouml;scht.');

define('HELPDESK_TEXT_PRODUCTS_ID_CHANGED', 'Artikelnummer ge&auml;ndert von <b>%1$s</b> nach <b>%2$s</b>.');
define('HELPDESK_TEXT_PRODUCTS_ID_ADDED', 'Artikelnummer <b>%1$s</b> hinzugef&uuml;gt.');
define('HELPDESK_TEXT_PRODUCTS_ID_DELETED', 'Artikelnummer <b>%1$s</b> gel&ouml;scht.');

define('HELPDESK_TEXT_DEPARTMENT_ID_CHANGED', 'Ticket wurde von der Abteilung <b>\'%1$s\'</b> zur Abteilung <b>\'%2$s\'</b> verschoben.');
define('HELPDESK_TEXT_DEPARTMENT_ID_ADDED', 'Ticket wurde zur Abteilung %1$s hinzugef&uuml;gt.');
 
define('HELPDESK_TEXT_PRIORITY_ID_CHANGED', 'Die Priorit&auml;t wurde von <b>\'%1$s\'</b> auf <b>\'%2$s\'</b> ge&auml;ndert.');

// E-Mail:
define('HELPDESK_TICKETS_EMAIL_CUSTOMER_NEW_ANSWER_SUBJECT', 'Antwort zu #{$ticket_no}');

 
?>