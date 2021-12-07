<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

jimport('joomla.application.component.view');
jimport( 'joomla.html.pane' );
class PhocaPDFCpViewPhocaPDFPlugin extends HtmlView
{
	protected $item;
	protected $items;
	protected $form;
	protected $state;
	protected $t;
	protected $r;

	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->items	= $this->get('Items');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		$this->t			= PhocaPDFUtils::setVars('plugin');
		$this->r            = new PhocaPDFRenderAdminView();

		//HTMLHelper::stylesheet( $this->t['s'] );


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {

			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		// Plugins
		//$this->tmpl['id']	= JFactory::getApplication()->input->get( 'id', 0, '', 'int' );
		$this->tmpl['id']	= $this->state->get('phocapdfplugin.id');

		$i = 0;

		$lang = Factory::getLanguage();
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

			$lang->load($value->name);

			$link		 = 'index.php?option=com_phocapdf&view=phocapdfplugin&task=phocapdfplugin.edit&extension_id='.(int)$value->extension_id;
			$value->link = '<a href="'.$link.'">'. str_replace('Phoca PDF ', '', Text::_($value->name)).'</a>';
			$i++;
		}


		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		$this->state		= $this->get('State');

		require_once JPATH_COMPONENT.'/helpers/phocapdfplugins.php';
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo	= PhocaPDFPluginsHelper::getActions($this->t);

		ToolbarHelper::title(   Text::_( 'COM_PHOCAPDF_PLUGINS' ), 'power-cord plugin' );

		$bar = Toolbar::getInstance( 'toolbar' );
		//$bar->appendButton( 'Link', 'back', 'COM_PHOCAPDF_CONTROL_PANEL', 'index.php?option=com_phocapdf' );

		$dhtml = '<a href="index.php?option=com_phocapdf" class="btn btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAPDF_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAPDF_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.edit')) {
			ToolbarHelper::apply('phocapdfplugin.apply', 'JTOOLBAR_APPLY');
			//JToolbarHelper::save('phocapdfplugin.save', 'JTOOLBAR_SAVE');
		}
		ToolbarHelper::divider();

		ToolbarHelper::help( 'screen.phocapdf', true );

	}
}
