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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
jimport('joomla.application.component.view');

class ContentViewArticle extends HtmlView
{
	protected $state;
	protected $item;
	protected $print;

	function display($tpl = null)
	{
		$app 		= Factory::getApplication();
		$user 		= Factory::getUser();


		$state 		= $this->get('State');
		$item 		= $this->get('Item');


		if (count($errors = $this->get('Errors'))){
			echo implode("\n", $errors);
			return false;
		}

		// Add router helpers.
		$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		$item->parent_slug = $item->category_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

		$item->readmore_link = '';

		$params 		= $state->get('params');

		$article_params 	= new Registry();
		$article_params->loadString($item->attribs);
		$active 		= $app->getMenu()->getActive();
		$temp = clone ($params);
		if ($active) {
			$currentLink = $active->link;
			if (strpos($currentLink, 'view=article'))
			{
				$article_params->merge($temp);
				$item->params = $article_params;
			}
			else
			{
				$temp->merge($article_params);
				$item->params = $temp;
			}
		} else {
			$temp->merge($article_params);
			$item->params = $temp;
		}

		$offset = $state->get('list.offset');


		// Check the access to the article
		$levels = $user->getAuthorisedViewLevels();
		if ((!in_array($item->access, $levels)) OR ((is_array($item->category_access)) AND (!in_array($item->category_access, $levels))))
		{
			// If a guest user, they may be able to log in to view the full article
			if (($params->get('show_noauth')) AND ($user->get('guest')))
			{
				// Redirect to login
				$uri = Uri::getInstance();
				$app->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri), Text::_('COM_CONTENT_ERROR_LOGIN_TO_VIEW_ARTICLE'));
				return;
			}
			else
			{
				echo Text::_('COM_PHOCAPDF_NOT_AUTHORIZED_DO_ACTION');
				return;
			}
		}


		
		/*
		if ($item->params->get('show_intro', 1) == 1) {
			$item->text = $item->introtext . ' ' . $item->fulltext;
		} else {
			$item->text = $item->fulltext;
		}*/
      
      	if ($item->params->get('show_intro', '1') == '1')
		{
			$item->text = $item->introtext . ' ' . $item->fulltext;
		}
		elseif ($item->fulltext)
		{
			$item->text = $item->fulltext;
		}
		else
		{
			$item->text = $item->introtext;
		}
		
		
		

		$item->article_text = $item->text; // Don't render the plugins, etc.

		//
		// Process the content plugins.
		//
		PluginHelper::importPlugin('content');
		$results = Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.article', &$item, &$params, $offset));

		$item->event = new stdClass();
		$results = Factory::getApplication()->triggerEvent('onContentAfterTitle', array('com_content.article', &$item, &$params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

	/*	$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));*/

		/*$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('user', $user);
		$this->assign('print', $print);*/

		// Override the layout.
		if ($layout = $params->get('layout'))
		{
			$this->setLayout($layout);
		}

		// Increment the hit counter of the article.
		if (!$params->get('intro_only') && $offset == 0)
		{
			$model = $this->getModel();
			$model->hit();
		}

		$document = Factory::getDocument();
		$document->setHeader($this->getHeaderText($item, $params));
		
		// Change the item output - e.g. add intro image to the content
		PluginHelper::importPlugin('phocapdf', 'content');
		
		//$content 	= new JObject();
		//JFactory::getApplication()->triggerEvent('onBeforeCreatePDFContent', array (&$content));
		//JFactory::getApplication()->triggerEvent('onBeforeRenderOutputContent', array (&$item, $content));	
		Factory::getApplication()->triggerEvent('onBeforeRenderOutputContent', array (&$item));
	

		echo $item->text;
		$document->setArticleText($item->article_text);
		$document->setArticleTitle($item->title);

		//parent::display($tpl);
	}

	protected function getHeaderText(& $item, & $params)
	{
		$text = '';

		if ($params->get('show_category')) {
			$text .= "\n";
			$title = $this->escape($item->category_title);
			$text .= Text::sprintf('COM_CONTENT_CATEGORY', $title);
		}


		if ($params->get('show_create_date') && $params->get('show_author')) {
			// Display Separator
			$text .= "\n";
		}

		if ($params->get('show_create_date')) {
			$text .= Text::sprintf('COM_CONTENT_CREATED_DATE_ON', HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC2')));
		}


		if ($params->get('show_modify_date') && ($params->get('show_author') || $params->get('show_create_date'))) {
			// Display Separator
			$text .= " - ";
		}

		if ($params->get('show_modify_date')) {
			$text .= Text::sprintf('COM_CONTENT_LAST_UPDATED', HTMLHelper::_('date',$item->modified, Text::_('DATE_FORMAT_LC2')));
		}

		if ($params->get('show_publish_date')) {
			$text .= "\n";
			$text .= Text::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', HTMLHelper::_('date', $item->publish_up, Text::_('DATE_FORMAT_LC2')));
		}

		if ($params->get('show_author') && !empty($item->author )) {

			$text .= "\n";
			$author=($item->created_by_alias ? $item->created_by_alias : $item->author);
			$text .= Text::sprintf('COM_CONTENT_WRITTEN_BY', $author);
		}

		if ($params->get('show_hits')) {
			$text .= "\n";
			$text .= Text::sprintf('COM_CONTENT_ARTICLE_HITS', $item->hits);
		}

		return $text;
	}
}
