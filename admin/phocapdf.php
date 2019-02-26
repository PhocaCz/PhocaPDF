<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/phocapdfrenderadmin.php' );
require_once( JPATH_COMPONENT.'/helpers/phocapdfutils.php' );
require_once( JPATH_COMPONENT.'/helpers/phocapdf.php' );
require_once( JPATH_COMPONENT.'/helpers/phocapdfparams.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaPDFCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>