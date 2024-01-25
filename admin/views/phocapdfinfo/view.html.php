<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );

class PhocaPDFCpViewPhocaPDFInfo extends HtmlView
{
	protected $t;
	protected $r;

	function display($tpl = null) {

		$this->t	= PhocaPdfUtils::setVars('info');
		$this->r	= new PhocaPdfRenderAdminview();
		$this->t['component_head'] 	= 'COM_PHOCAPDF_PHOCA_PDF';
		$this->t['component_links']	= $this->r->getLinks(1);
		$this->t['version'] = PhocaPDFHelper::getPhocaVersion('com_phocapdf');
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		ToolbarHelper::title( Text::_( 'COM_PHOCAPDF_PDF_INFO' ), 'info.png' );
		//JToolbarHelper::cancel( 'cancel', 'COM_PHOCAPDF_CLOSE' );

		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocapdf" class="btn btn-primary btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAPDF_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAPDF_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocapdf', true );
	}
}
?>
