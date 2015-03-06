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

class PhocaPDFCpViewPhocaPDFInfo extends JViewLegacy
{	
	protected $t;
	
	function display($tpl = null) {
		
		$this->t	= PhocaPdfUtils::setVars('info');
		JHTML::stylesheet( $this->t['s'] );
		$this->t['version'] = PhocaPDFHelper::getPhocaVersion('com_phocapdf');
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		JToolBarHelper::title( JText::_( 'COM_PHOCAPDF_PDF_INFO' ), 'info.png' );
		//JToolBarHelper::cancel( 'cancel', 'COM_PHOCAPDF_CLOSE' );
		
		$bar = JToolBar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocapdf" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCAPDF_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAPDF_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocapdf', true );
	}
}
?>