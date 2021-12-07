<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$r = $this->r;
$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $this->t['o']);
$saveOrder	= $listOrder == 'a.ordering';
$saveOrderingUrl = '';
if ($saveOrder && !empty($this->items)) {
	$saveOrderingUrl = $r->saveOrder($this->t, $listDirn);
}
$sortFields = $this->getSortFields();

echo $r->jsJorderTable($listOrder);


echo $r->startForm($this->t['o'], $this->t['tasks'], 'adminForm');
//echo $r->startFilter($this->t['l'].'_FILTER', 0);
//echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
//echo $r->endFilter();

echo $r->startMainContainer();
//echo $r->startFilterBar();
//echo $r->inputFilterSearch($this->t['l'].'_FILTER_SEARCH_LABEL', $this->t['l'].'_FILTER_SEARCH_DESC',
//							$this->escape($this->state->get('filter.search')));
//echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
//echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
//echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
//echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);
//echo $r->endFilterBar();
echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

echo $r->startTable('categoryList');

echo $r->startTblHeader();

//echo $r->thOrdering('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
//echo '<th></th>';
//echo $r->thCheck('JGLOBAL_CHECK_ALL');
echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);
echo '<th class="ph-titlefont">'.Text::_($this->t['l'].'_FONT_NAME').'</th>'."\n";
echo '<th class="ph-delete">'.Text::_($this->t['l'].'_DELETE').'</th>'."\n";

echo $r->endTblHeader();

echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

//echo '<tbody>'. "\n";

$originalOrders = array();
$parentsStr 	= "";
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {

		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
			$j++;
/*
$urlEdit		= 'index.php?option='.$this->t['o'].'&task='.$this->t['task'].'.edit&id=';
$orderkey   	= array_search($item->id, $this->ordering[0]);
$ordering		= ($listOrder == 'a.ordering');
$canCreate		= $user->authorise('core.create', $this->t['o']);
$canEdit		= $user->authorise('core.edit', $this->t['o']);
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $this->t['o']) && $canCheckin;
$linkEdit 		= JRoute::_( $urlEdit.(int) $item->id );*/
$orderkey   	= 0;//array_search($item->id, $this->ordering[$item->catid]);
$canDelete		= $user->authorise('core.delete', 'com_phocapdf');
$linkRemove 	= 'javascript:void(0);';
$onClickRemove 	= 'javascript:if (confirm(\''.Text::_('COM_PHOCAPDF_WARNING_DELETE_ITEMS').'\')){'
				 .' return Joomla.listItemTask(\'cb'. $i .'\',\'phocapdffont.delete\');'
				 .'}';
$canChange		= false;

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, 0);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, 0);

//echo $r->td(HTMLHelper::_('grid.id', $i, $item->id), "small");

echo $r->td($this->escape($item->name), "small");

$del = '';
if ($canDelete) {
$del = '<a href="'. $linkRemove.'" onclick="'.$onClickRemove.'" title="'. Text::_('COM_PHOCAPDF_DELETE').'">'
	//. HTMLHelper::_('image', 'media/com_phocapdf/images/administrator/icon-16-trash.png', Text::_('COM_PHOCAPDF_DELETE') )
    . '<span class="icon-fw icon-trash phi-fc-rd" title="'.Text::_('COM_PHOCAPDF_DELETE').'"></span>'
	.'</a>';
}
echo $r->td($del, "small ph-center");


echo $r->endTr();

		//}
	}
}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 4);
echo $r->endTable();



echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();

echo '<div class="clearfix"></div>';

echo '<div id="j-main-container" class="span12">';
echo '<div class="ph-cp-hr"></div>';


$rw = new PhocaPdfRenderAdminView();
$activeTab = 'upload';
echo $rw->startTabs($activeTab);
$tabs = array();
$tabs['upload'] = '<span class="ph-cp-item"><i class="phi phi-fs-s phi-fc-bd duotone icon-upload"></i></span>' . '&nbsp;'.Text::_('COM_PHOCAPDF_UPLOAD_PHOCAPDF_FONT_INSTALL_FILE');
echo $rw->navigation($tabs, $activeTab);

echo $rw->startTab('upload', $tabs['upload'], $activeTab == 'upload' ? 'active' : '');


?><form enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_phocapdf&view=phocapdffonts'); ?>" method="post" name="uploadForm">

<?php   if ($this->ftp) { echo PhocaPDFRender::renderFTPaccess();} ?>

<h4><?php echo Text::_( 'COM_PHOCAPDF_UPLOAD_PHOCAPDF_FONT_INSTALL_FILE' ); ?></h4>
<input type="file" id="sfile-upload" class="input" name="Filedata" />
<button class="btn btn-primary" id="upload-submit"><i class="icon-upload icon-white"></i><?php echo Text::_( 'COM_PHOCAPDF_UPLOAD_FILE' ); ?> &amp; <?php echo Text::_( 'COM_PHOCAPDF_INSTALL' ); ?></button>

<input type="hidden" name="type" value="" />
<input type="hidden" name="task" value="install" />
<input type="hidden" name="option" value="com_phocapdf" />
<input type="hidden" name="task" value="phocapdffont.install" />
<?php
echo HTMLHelper::_( 'form.token' );
echo '</form>';

echo $rw->endTab();
echo $rw->endTabs();



echo '<div class="ph-cp-hr"></div>';

echo '<div class="btn-group" style="float:left;"><a class="btn btn-large btn-info" href="https://www.phoca.cz/phocapdf-fonts" target="_blank"><i class="icon-share icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'] . '_CHECK_FOR_AVAILABLE_FONTS') .'</a></div>';
echo '<div class="clearfix"></div>';

echo '</div>';

//if ($this->t['tab'] != '') {$jsCt = 'a[href=#'.$this->t['tab'] .']';} else {$jsCt = 'a:first';}
$jsCt = 'a:first';
echo '<script type="text/javascript">';
echo '   jQuery(\'#configTabs '.$jsCt.'\').tab(\'show\');'; // Select first tab
echo '</script>';
