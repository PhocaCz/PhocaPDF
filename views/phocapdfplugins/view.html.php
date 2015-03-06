<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.html.pane' );
jimport( 'joomla.application.component.view' );

class PhocaPDFCpViewPhocaPDFPlugins extends JViewLegacy
{
	protected $items;
	
	function display($tpl = null) {
		
		$this->items		= $this->get('Items');
		$this->t			= PhocaPDFUtils::setVars('plugin');
		JHTML::stylesheet( $this->t['s'] );
		
		// If there is one, select it
		if (isset($this->items[0]->extension_id) && (int)$this->items[0]->extension_id > 0) {
			$app	= JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_phocapdf&view=phocapdfplugin&task=phocapdfplugin.edit&extension_id='.(int)$this->items[0]->extension_id, false));
			return;
		}
		
		$this->addToolbar();
		echo JText::_('COM_PHOCAPDF_NO_PHOCAPDF_PLUGIN_INSTALLED');
	}
	
	
	protected function addToolbar() {
	
		$this->state		= $this->get('State');
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocapdfplugins.php';
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo	= PhocaPDFPluginsHelper::getActions($this->t);
		
		JToolBarHelper::title(   JText::_( 'COM_PHOCAPDF_PLUGINS' ), 'power-cord plugin' );
		
		$bar = JToolBar::getInstance( 'toolbar' );
		$bar->appendButton( 'Link', 'home-2 cpanel', 'COM_PHOCAPDF_CONTROL_PANEL', 'index.php?option=com_phocapdf' );

		JToolBarHelper::divider();
		
		JToolBarHelper::help( 'screen.phocapdf', true );
		
	}
}
?>