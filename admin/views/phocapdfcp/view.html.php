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

class PhocaPDFCpViewPhocaPDFCp extends HtmlView
{
	protected $t;
	protected $r;
	protected $views;

	public function display($tpl = null) {

		$this->t	= PhocaPDFUtils::setVars('cp');
		$this->r	= new PhocaPdfRenderAdminview();
		$i = ' icon-';
		$d = 'duotone ';

		$this->views= array(
		'plugins'	=> array($this->t['l'] . '_PLUGINS', $d.$i.'modules', '#CC0033'),
		'fonts'		=> array($this->t['l'] . '_FONTS', $d.$i.'multilingual', '#9900CC'),
		'info'		=> array($this->t['l'] . '_INFO', $d.$i.'info-circle', '#3378cc')
		);


		$this->t['version'] = PhocaPDFHelper::getPhocaVersion('com_phocapdf');
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocapdfcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaPdfHelperControlPanel::getActions($this->t);
		ToolbarHelper::title( Text::_( 'COM_PHOCAPDF_PDF_CONTROL_PANEL' ), 'home-2 cpanel' );

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocapdf" class="btn btn-primary btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAPDF_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAPDF_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocapdf');
			ToolbarHelper::divider();
		}

		ToolbarHelper::help( 'screen.phocapdf', true );
	}
}
?>
