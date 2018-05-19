<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');
/*********** XML PARAMETERS AND VALUES ************/
$xml_item = "component";// component | template
$xml_file = "phocapdf.xml";		
$xml_name = "com_phocapdf";
$xml_creation_date = "19/05/2018";
$xml_author = "Jan Pavelka (www.phoca.cz)";
$xml_author_email = "";
$xml_author_url = "www.phoca.cz";
$xml_copyright = "Jan Pavelka";
$xml_license = "GNU/GPL";
$xml_version = "3.0.5";
$xml_description = "Phoca PDF";
$xml_copy_file = 1;//Copy other files in to administration area (only for development), ./front, ./language, ./other
$xml_script_file = 'install/script.php';

$xml_menu = array (0 => "COM_PHOCAPDF", 1 => "option=com_phocapdf", 2 => "media/com_phocapdf/images/administrator/icon-16-ppdf-menu.png", 4 => 'phocapdfcp');
$xml_submenu[0] = array (0 => "COM_PHOCAPDF_CONTROLPANEL", 1 => "option=com_phocapdf", 2 => "media/com_phocapdf/images/administrator/icon-16-ppdf-cp.png", 4 => 'phocapdfcp');
$xml_submenu[1] = array (0 => "COM_PHOCAPDF_PLUGINS", 1 => "option=com_phocapdf&view=phocapdfplugins", 2 => "media/com_phocapdf/images/administrator/icon-16-ppdf-pdf.png", 4 => 'phocapdfplugins');
$xml_submenu[2] = array (0 => "COM_PHOCAPDF_FONTS", 1 => "option=com_phocapdf&view=phocapdffonts", 2 => "media/com_phocapdf/images/administrator/icon-16-ppdf-font.png", 4 => 'phocapdffonts');
$xml_submenu[3] = array (0 => "COM_PHOCAPDF_INFO", 1 => "option=com_phocapdf&view=phocapdfinfo", 2 => "media/com_phocapdf/images/administrator/icon-16-ppdf-info.png", 4 => 'phocapdfinfo');

$xml_install_file = 'install.phocapdf.php'; 
$xml_uninstall_file = 'uninstall.phocapdf.php';
/*********** XML PARAMETERS AND VALUES ************/
?>