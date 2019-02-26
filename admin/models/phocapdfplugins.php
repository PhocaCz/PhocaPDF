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

defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

class PhocaPDFCpModelPhocaPDFPlugins extends JModelList
{
	protected	$option 		= 'com_phocapdf';
	
	protected function getListQuery()
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

	
		
		return $query;
	}
}
?>