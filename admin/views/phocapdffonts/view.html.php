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
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
jimport( 'joomla.application.component.view' );
jimport('joomla.client.helper');
jimport('joomla.filesystem.file');

class PhocaPDFCpViewPhocaPDFFonts extends HtmlView
{
	protected $ftp;
	protected $state;
	protected $items;
	protected $pagination;
	protected $t;
	protected $r;

	function display($tpl = null) {

		$this->t			= PhocaPDFUtils::setVars('font');
		$this->r 			= new PhocaPdfRenderAdminViews();
		$this->ftp			= ClientHelper::setCredentialsFromRequest('ftp');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		HTMLHelper::stylesheet( $this->t['s'] );
		HTMLHelper::stylesheet( 'media/com_phocapdf/css/administrator/style.css' );

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {


		$this->state		= $this->get('State');
		require_once JPATH_COMPONENT.'/helpers/phocapdffonts.php';
		$canDo	= PhocaPDFFontsHelper::getActions($this->t);

		ToolbarHelper::title(   Text::_( 'COM_PHOCAPDF_FONTS' ), 'font' );

		// Correct Joomla! problem
		$app = Factory::getApplication();
		$app->JComponentTitle = str_replace('icon-', 'phicon-', $app->JComponentTitle);

		//$bar = JToolbar::getInstance( 'toolbar' );
		//$bar->appendButton( 'Link', 'back', 'COM_PHOCAPDF_CONTROL_PANEL', 'index.php?option=com_phocapdf' );

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( Text::_( 'COM_PHOCAPDF_WARNING_DELETE_ITEMS' ), 'phocapdffont.delete', 'COM_PHOCAPDF_DELETE');
		}
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocapdf', true );
	}

	protected function getSortFields() {
		return array(

		);
	}
}
?>
