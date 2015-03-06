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

class PhocaPDFCpViewPhocaPDFCp extends JViewLegacy
{
	protected $t;
	
	public function display($tpl = null) {
	
		$this->t	= PhocaPDFUtils::setVars('cp');
		$this->views= array(
		'plugins'	=> $this->t['l'] . '_PLUGINS',
		'fonts'		=> $this->t['l'] . '_FONTS',
		'info'		=> $this->t['l'] . '_INFO'
		);
		
		JHTML::stylesheet( $this->t['s'] );
		JHTML::_('behavior.tooltip');
		$this->t['version'] = PhocaPDFHelper::getPhocaVersion('com_phocapdf');
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocapdfcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaPdfHelperControlPanel::getActions($this->t);
		JToolBarHelper::title( JText::_( 'COM_PHOCAPDF_PDF_CONTROL_PANEL' ), 'home-2 cpanel' );
		
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolBar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocapdf" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCAPDF_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAPDF_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_phocapdf');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help( 'screen.phocapdf', true );
	}
}
?>