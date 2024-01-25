<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;
jimport('joomla.application.component.controller');
$app		= Factory::getApplication();
$option 	= $app->input->get('option');

$l['cp']	= array('COM_PHOCAPDF_CONTROL_PANEL', '');
$l['p']		= array('COM_PHOCAPDF_PLUGINS', 'phocapdfplugins');
$l['f']		= array('COM_PHOCAPDF_FONTS', 'phocapdffonts');
$l['i']		= array('COM_PHOCAPDF_INFO', 'phocapdfinfo');

// Submenu view
$view	= Factory::getApplication()->input->get('view');
$layout	= Factory::getApplication()->input->get('layout');


if ($layout == 'edit') {
} else {
	foreach ($l as $k => $v) {

		if ($v[1] == '') {
			$link = 'index.php?option='.$option;
		} else {
			$link = 'index.php?option='.$option.'&view=';
		}

		if ($view == $v[1]) {
			Sidebar::addEntry(Text::_($v[0]), $link.$v[1], true );
		} else {
			Sidebar::addEntry(Text::_($v[0]), $link.$v[1]);
		}
	}
}
class phocaPdfCpController extends BaseController
{
	function display($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);
	}
}
?>
