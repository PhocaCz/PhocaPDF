<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

jimport('joomla.application.component.controllerform');


class PhocaPDFCpControllerPhocaPDFPlugin extends FormController
{
	protected	$option 		= 'com_phocapdf';

	function __construct($config=array()) {
		
		parent::__construct($config);
	}
	
	public function execute($task)
	{
		
		parent::execute($task);
		// Clear the component's cache
		if ($task != 'display') {
			$cache = Factory::getCache('com_phocapdf');
			$cache->clean();
		}
	}
	
	protected function allowAdd($data = array()) {
		$user		= Factory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.create', 'com_phocapdf');
		if ($allow === null) {
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id') {
		$user		= Factory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.edit', 'com_phocapdf');
		if ($allow === null) {
			return parent::allowEdit($data, $key);
		} else {
			return $allow;
		}
	}
	
	
}