<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Filesystem\Path;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;

use Joomla\Registry\Registry;

jimport('joomla.application.component.modeladmin');


class PhocaPDFCpModelPhocaPDFPlugin extends AdminModel
{
	protected $option				= 'com_phocapdf';
	protected $_cache;
	public 	$typeAlias 				= 'com_phocapdf.phocapdfplugin';
	protected $event_after_save 	= 'onExtensionAfterSave';
	protected $event_before_save 	= 'onExtensionBeforeSave';
	//protected $formName             = 'phocapdfplugin';

	/*protected $event_after_delete = null;
	protected $event_after_save = null;
	protected $event_before_delete = null;
	protected $event_before_save = null;
	protected $event_change_state = null;*/

	public function getForm($data = array(), $loadData = true)
	{


		// The folder and element vars are passed when saving the form.
		if (empty($data)) {
			$item		= $this->getItem();
			$folder		= $item->folder;
			$element	= $item->element;
		} else {
			$folder		= ArrayHelper::getValue($data, 'folder', '', 'cmd');
			$element	= ArrayHelper::getValue($data, 'element', '', 'cmd');
		}

		// These variables are used to add data from the plugin XML files.
		$this->setState('item.folder',	$folder);
		$this->setState('item.element',	$element);


		// LOAD RIGHT PLUGIN LANGAUGE INTO FORM
        if (isset($item->name) && $item->name != '') {
            $app = Factory::getApplication();
		    $lang = $app->getLanguage();
		    $lang->load($item->name);

        }

        // Get the form.
		$form = $this->loadForm('com_phocapdf.phocapdfplugin', 'phocapdfplugin', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('enabled', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('enabled', 'filter', 'unset');
		}

		return $form;
	}


	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocapdf.edit.phocapdfplugin.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}


	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('phocapdfplugin.id');

		if (!isset($this->_cache[$pk])) {
			//$false	= false;

			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError()) {

				throw new Exception($table->getError(), 500);
				return $false;
			}

			// Convert to the JObject before adding other data.
			$prop = $table->getProperties(1);
			$this->_cache[$pk] = ArrayHelper::toObject($prop, CMSObject::class);

			// Convert the params field to an array.

            // Convert the params field to an array.
			$registry = new Registry($table->params);
            /*if (isset($item->params)) {

                $registry->loadString($table->params);

            }*/
            $this->_cache[$pk]->params = $registry->toArray();

			// Get the plugin XML.

			$client	= ApplicationHelper::getClientInfo($table->client_id);
			$path	= Path::clean($client->path.'/plugins/'.$table->folder.'/'.$table->element.'/'.$table->element.'.xml');

			if (file_exists($path)) {
				$this->_cache[$pk]->xml = simplexml_load_file($path);
			} else {
				$this->_cache[$pk]->xml = null;
			}


		}

		return $this->_cache[$pk];
	}


	public function getTable($type = 'Extension', $prefix = 'JTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	protected function populateState()
	{
		// Execute the parent method.
		parent::populateState();

		$app = Factory::getApplication('administrator');

		// Load the User state.
		$pk = (int) $app->input->get('extension_id');
		$this->setState('phocapdfplugin.id', $pk);
	}

	protected function preprocessForm(Form $form, $data, $group = 'phocapdf')
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Initialise variables.
		$folder		= $this->getState('item.folder');
		$element	= $this->getState('item.element');
		$lang		= Factory::getLanguage();
		$client		= ApplicationHelper::getClientInfo(0);

		if (empty($folder) || empty($element)) {
			$app = Factory::getApplication();
			$app->redirect(Route::_('index.php?option=com_phocapdf&view=phocapdfcp',false), Text::_('COM_PHOCAPDF_NO_FOLDER_OR_ELEMENT_FOUND'));
		}
		// Try 1.6 format: /plugins/folder/element/element.xml
		$formFile = Path::clean($client->path.'/plugins/'.$folder.'/'.$element.'/'.$element.'.xml');
		if (!file_exists($formFile)) {
			// Try 1.5 format: /plugins/folder/element/element.xml
			$formFile = Path::clean($client->path.'/plugins/'.$folder.'/'.$element.'.xml');
			if (!file_exists($formFile)) {
				throw new Exception(Text::sprintf('COM_PHOCAPDF_ERROR_FILE_NOT_FOUND', $element.'.xml'));
				return false;
			}
		}

		// Load the core and/or local language file(s).
			$lang->load('plg_'.$folder.'_'.$element, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, $client->path.'/plugins/'.$folder.'/'.$element, null, false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, $client->path.'/plugins/'.$folder.'/'.$element, $lang->getDefault(), false, false);

		if (file_exists($formFile)) {
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//config')) {
				throw new Exception(Text::_('COM_PHOCAPDF_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile)) {
			throw new Exception(Text::_('COM_PHOCAPDF_LOADFILE_FAILED'));
		}

		// Get the help data from the XML file if present.
		$help = $xml->xpath('/extension/help');
		if (!empty($help)) {
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data);
	}

	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'type = '. $this->_db->Quote($table->type);
		$condition[] = 'folder = '. $this->_db->Quote($table->folder);
		return $condition;
	}


	public function save($data)
	{
		// Load the extension plugin group.
		PluginHelper::importPlugin('extension');

		// Setup type
		$data['type'] = 'plugin';


		return parent::save($data);
	}

	/*
	 *
	 */
	 public function getItems()
	{
		/*
		$query = 'SELECT p.name, p.id, p.published, u.name AS editor, g.name AS groupname'
			. ' FROM #__plugins AS p'
			. ' LEFT JOIN #__users AS u ON u.id = p.checked_out'
			. ' LEFT JOIN #__groups AS g ON g.id = p.access'
			. ' WHERE p.folder = '.$db->Quote('phocapdf')
			. ' OR ( p.folder = '.$db->Quote('system').' AND p.element = '.$db->Quote('phocapdfcontent').')'
			. ' GROUP BY p.id';
		*/
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__extensions` AS a');


		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		$query->where('a.type = "plugin"');
		$query->where('a.folder = "phocapdf"');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol != 'a.ordering') {
			$orderCol = 'a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		$db->setQuery( $query );
		$items = $db->loadObjectList();

		return $items;
	}
}
