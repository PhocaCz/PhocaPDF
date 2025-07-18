<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Site\View\Article;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\AssociationHelper;
use Joomla\Component\Content\Site\Helper\RouteHelper;

/**
 * HTML Article View class for the Content component
 *
 * @since  1.5
 */
class PdfView extends BaseHtmlView
{
	/**
	 * The article object
	 *
	 * @var  \stdClass
	 */
	protected $item;

	/**
	 * The page parameters
	 *
	 * @var    \Joomla\Registry\Registry|null
	 * @since  4.0.0
	 */
	protected $params = null;

	/**
	 * Should the print button be displayed or not?
	 *
	 * @var  boolean
	 */
	protected $print = false;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * The user object
	 *
	 * @var  \JUser|null
	 */
	protected $user = null;

	/**
	 * The page class suffix
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $pageclass_sfx = '';

	/**
	 * The flag to mark if the active menu item is linked to the being displayed article
	 *
	 * @var boolean
	 */
	protected $menuItemMatchArticle = false;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		if ($this->getLayout() == 'pagebreak')
		{
			parent::display($tpl);

			return;
		}

		$app        = Factory::getApplication();
		$user       = Factory::getUser();

		$this->item  = $this->get('Item');
		$this->print = $app->input->getBool('print', false);
		$this->state = $this->get('State');
		$this->user  = $user;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Create a shortcut for $item.
		$item            = $this->item;
		$item->tagLayout = new FileLayout('joomla.content.tags');

		// Add router helpers.
		$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

		// No link for ROOT category
		if ($item->parent_alias === 'root')
		{
			$item->parent_id = null;
		}

		// TODO: Change based on shownoauth
		$item->readmore_link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));

		// Merge article params. If this is single-article view, menu params override article params
		// Otherwise, article params override menu item params
		$this->params = $this->state->get('params');
		$active       = $app->getMenu()->getActive();
		$temp         = clone $this->params;

		// Check to see which parameters should take priority. If the active menu item link to the current article, then
		// the menu item params take priority
		if ($active
			&& $active->component == 'com_content'
			&& isset($active->query['view'], $active->query['id'])
			&& $active->query['view'] == 'article'
			&& $active->query['id'] == $item->id)
		{
			$this->menuItemMatchArticle = true;

			// Load layout from active query (in case it is an alternative menu item)
			if (isset($active->query['layout']))
			{
				$this->setLayout($active->query['layout']);
			}
			// Check for alternative layout of article
			elseif ($layout = $item->params->get('article_layout'))
			{
				$this->setLayout($layout);
			}

			// $item->params are the article params, $temp are the menu item params
			// Merge so that the menu item params take priority
			$item->params->merge($temp);
		}
		else
		{
			// The active menu item is not linked to this article, so the article params take priority here
			// Merge the menu item params with the article params so that the article params take priority
			$temp->merge($item->params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-article menu item)
			// Single-article menu item layout takes priority over alt layout for an article
			if ($layout = $item->params->get('article_layout'))
			{
				$this->setLayout($layout);
			}
		}

		$offset = $this->state->get('list.offset');

		// Check the view access to the article (the model has already computed the values).
		if ($item->params->get('access-view') == false && ($item->params->get('show_noauth', '0') == '0'))
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return;
		}

		/**
		 * Check for no 'access-view' and empty fulltext,
		 * - Redirect guest users to login
		 * - Deny access to logged users with 403 code
		 * NOTE: we do not recheck for no access-view + show_noauth disabled ... since it was checked above
		 */
		if ($item->params->get('access-view') == false && !strlen($item->fulltext))
		{
			if ($this->user->get('guest'))
			{
				$return = base64_encode(Uri::getInstance());
				$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'notice');
				$app->redirect($login_url_with_return, 403);
			}
			else
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->setHeader('status', 403, true);

				return;
			}
		}

		/**
		 * NOTE: The following code (usually) sets the text to contain the fulltext, but it is the
		 * responsibility of the layout to check 'access-view' and only use "introtext" for guests
		 */
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

		$item->tags = new TagsHelper;
		$item->tags->getItemTags('com_content.article', $this->item->id);

		if (Associations::isEnabled() && $item->params->get('show_associations'))
		{
			$item->associations = AssociationHelper::displayAssociations($item->id);
		}

		// Process the content plugins.
		PluginHelper::importPlugin('content');
		Factory::getApplication()->triggerEvent('onContentPrepare', array('com_content.article', &$item, &$item->params, $offset));

		$item->event = new \stdClass;
		$results = Factory::getApplication()->triggerEvent('onContentAfterTitle', array('com_content.article', &$item, &$item->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));
/*
		$results = Factory::getApplication()->triggerEvent('onContentBeforeDisplay', array('com_content.article', &$item, &$item->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = Factory::getApplication()->triggerEvent('onContentAfterDisplay', array('com_content.article', &$item, &$item->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));
*/
		// Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars((string)$this->item->params->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

		//$this->_prepareDocument();

		$document = Factory::getDocument();
		$document->setHeader($this->getHeaderText($item, $item->params));
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
			$text .= Text::sprintf('COM_CONTENT_CREATED_DATE_ON', HtmlHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC2')));
		}


		if ($params->get('show_modify_date') && ($params->get('show_author') || $params->get('show_create_date'))) {
			// Display Separator
			$text .= " - ";
		}

		if ($params->get('show_modify_date')) {
			$text .= Text::sprintf('COM_CONTENT_LAST_UPDATED', HtmlHelper::_('date',$item->modified, Text::_('DATE_FORMAT_LC2')));
		}

		if ($params->get('show_publish_date')) {
			$text .= "\n";
			$text .= Text::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', HtmlHelper::_('date', $item->publish_up, Text::_('DATE_FORMAT_LC2')));
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

	/**
	 * Prepares the document.
	 *
	 * @return  void
	 */
	/*
	protected function _prepareDocument()
	{
		$app     = Factory::getApplication();
		$pathway = $app->getPathway();

		/**
		 * Because the application sets a default page title,
		 * we need to get it from the menu item itself
		 *//*
		$menu = $app->getMenu()->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');

		// If the menu item is not linked to this article
		if (!$this->menuItemMatchArticle)
		{
			// If a browser page title is defined, use that, then fall back to the article title if set, then fall back to the page_title option
			$title = $this->item->params->get('article_page_title', $this->item->title ?: $title);

			// Get ID of the category from active menu item
			if ($menu && $menu->component == 'com_content' && isset($menu->query['view'])
				&& in_array($menu->query['view'], ['categories', 'category']))
			{
				$id = $menu->query['id'];
			}
			else
			{
				$id = 0;
			}

			$path     = array(array('title' => $this->item->title, 'link' => ''));
			$category = Categories::getInstance('Content')->get($this->item->catid);

			while ($category !== null && $category->id != $id && $category->id !== 'root')
			{
				$path[]   = array('title' => $category->title, 'link' => RouteHelper::getCategoryRoute($category->id, $category->language));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title))
		{
			$title = $this->item->title;
		}

		$this->setDocumentTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}

		if ($app->get('MetaAuthor') == '1')
		{
			$author = $this->item->created_by_alias ?: $this->item->author;
			$this->document->setMetaData('author', $author);
		}

		$mdata = $this->item->metadata->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetaData($k, $v);
			}
		}

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->title = $this->item->title . ' - ' . $this->item->page_title;
			$this->setDocumentTitle(
				$this->item->page_title . ' - ' . Text::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1)
			);
		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}*/
}
