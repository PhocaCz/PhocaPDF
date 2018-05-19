<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
jimport('joomla.client.helper');
jimport('joomla.filesystem.file');

class PhocaPDFCpViewPhocaPDFFonts extends JViewLegacy
{
	protected $ftp;
	protected $state;
	protected $items;
	protected $pagination;
	protected $t;
	
	function display($tpl = null) {
			
		$this->t			= PhocaPDFUtils::setVars('font');
		$this->ftp			= JClientHelper::setCredentialsFromRequest('ftp');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		JHTML::stylesheet( $this->t['s'] );
		JHTML::stylesheet( 'media/com_phocapdf/css/administrator/style.css' );
		
		$this->addToolbar();
		parent::display($tpl);	
	}
	
	protected function addToolbar() {
	
		
		$this->state		= $this->get('State');
		require_once JPATH_COMPONENT.'/helpers/phocapdffonts.php';
		$canDo	= PhocaPDFFontsHelper::getActions($this->t);
		
		JToolbarHelper::title(   JText::_( 'COM_PHOCAPDF_FONTS' ), 'font' );
		
		// Correct Joomla! problem
		$app = JFactory::getApplication();
		$app->JComponentTitle = str_replace('icon-', 'phicon-', $app->JComponentTitle);
		
		//$bar = JToolbar::getInstance( 'toolbar' );
		//$bar->appendButton( 'Link', 'back', 'COM_PHOCAPDF_CONTROL_PANEL', 'index.php?option=com_phocapdf' );
		
		if ($canDo->get('core.delete')) {
			JToolbarHelper::deleteList( JText::_( 'COM_PHOCAPDF_WARNING_DELETE_ITEMS' ), 'phocapdffont.delete', 'COM_PHOCAPDF_DELETE');
		}
		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.phocapdf', true );
	}
	
	protected function getSortFields() {
		return array(
			
		);
	}
}	
?>
