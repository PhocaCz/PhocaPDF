<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocapdfrenderadmin.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocapdfutils.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocapdf.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocapdfparams.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminview.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminviews.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaPDFCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>