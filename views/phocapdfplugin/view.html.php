<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport( 'joomla.html.pane' );
class PhocaPDFCpViewPhocaPDFPlugin extends JViewLegacy
{
	protected $item;
	protected $items;
	protected $form;
	protected $state;
	protected $t;

	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->items	= $this->get('Items');
		$this->item		= $this->get('Item');	
		$this->form		= $this->get('Form');
		
		$this->t			= PhocaPDFUtils::setVars('plugin');
		JHTML::stylesheet( $this->t['s'] );
		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		// Plugins
		//$this->tmpl['id']	= JRequest::getVar( 'id', 0, '', 'int' );
		$this->tmpl['id']	= $this->state->get('phocapdfplugin.id');
		
		$i = 0;
		
		foreach ($this->items as $key => $value) {
		
			if ((int)$this->tmpl['id'] > 0) {
				if ($value->extension_id == (int)$this->tmpl['id']) {
					$value->current = 'class="current"';
				} else {
					$value->current = '';
				}
			} else {
				if ($i == 0) {
					$value->current = 'class="current"';
					$this->tmpl['id'] 	= (int)$value->extension_id;
				} else {
					$value->current = '';
				}
			}
			$value->name = str_replace('Phoca PDF - ', '', $value->name);
			$link		 = 'index.php?option=com_phocapdf&view=phocapdfplugin&task=phocapdfplugin.edit&extension_id='.(int)$value->extension_id;
			$value->link = '<a href="'.$link.'">'.JText::_($value->name).'</a>';
			$i++;
		}
		
	
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
	
		$this->state		= $this->get('State');
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocapdfplugins.php';
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo	= PhocaPDFPluginsHelper::getActions($this->t);
		
		JToolBarHelper::title(   JText::_( 'COM_PHOCAPDF_PLUGINS' ), 'power-cord plugin' );
		
		$bar = JToolBar::getInstance( 'toolbar' );
		//$bar->appendButton( 'Link', 'back', 'COM_PHOCAPDF_CONTROL_PANEL', 'index.php?option=com_phocapdf' );

		$dhtml = '<a href="index.php?option=com_phocapdf" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCAPDF_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAPDF_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('phocapdfplugin.apply', 'JTOOLBAR_APPLY');
			//JToolBarHelper::save('phocapdfplugin.save', 'JTOOLBAR_SAVE');
		}
		JToolBarHelper::divider();
		
		JToolBarHelper::help( 'screen.phocapdf', true );
		
	}
}
