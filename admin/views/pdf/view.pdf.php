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
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.view');
 // Abstract view to e.g. not load all the VirtueMart page before creating PDF
class PhocaPDFCpViewPDF extends JViewLegacy
{
	function display($tpl = null) {
		parent::display($tpl);
	}
}