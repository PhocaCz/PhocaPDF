use Joomla\CMS\Document\DocumentRenderer;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Layout\LayoutHelper;
<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JDocument Modules renderer
 *
 * @since  3.5
 */
class DocumentRendererPDFModules extends DocumentRenderer
{
	/**
	 * Renders multiple modules script and returns the results as a string
	 *
	 * @param   string  $position  The position of the modules to render
	 * @param   array   $params    Associative array of values
	 * @param   string  $content   Module content
	 *
	 * @return  string  The output of the script
	 *
	 * @since   3.5
	 */
	public function render($position, $params = array(), $content = null)
	{
		$renderer = $this->_doc->loadRenderer('module');
		$buffer   = '';

		$app          = Factory::getApplication();
		$user         = Factory::getUser();
		$frontediting = ($app->isSite() && $app->get('frontediting', 1) && !$user->guest);
		$menusEditing = ($app->get('frontediting', 1) == 2) && $user->authorise('core.edit', 'com_menus');

		foreach (ModuleHelper::getModules($position) as $mod)
		{
			$moduleHtml = $renderer->render($mod, $params, $content);

			if ($frontediting && trim($moduleHtml) != '' && $user->authorise('module.edit.frontend', 'com_modules.module.' . $mod->id))
			{
				$displayData = array('moduleHtml' => &$moduleHtml, 'module' => $mod, 'position' => $position, 'menusediting' => $menusEditing);
				LayoutHelper::render('joomla.edit.frontediting_modules', $displayData);
			}

			$buffer .= $moduleHtml;
		}

		return $buffer;
	}
}
