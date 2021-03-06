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
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">

<div id="j-sidebar-container" class="span2"><?php echo JHtmlSidebar::render(); ?></div>

<div id="j-main-container" class="span10">
	<div class="adminform">
		<div class="ph-cpanel-left">
			<div id="cpanel"><?php

$class	= $this->t['n'] . 'RenderAdmin';
$link	= 'index.php?option='.$this->t['o'].'&view=';
foreach ($this->views as $k => $v) {
	$linkV	= $link . $this->t['c'] . $k;
	echo $class::quickIconButton( $linkV, 'icon-48-'.$k.'.png', JText::_($v), $this->t['i']);
}
				?><div style="clear:both">&nbsp;</div>
				<p>&nbsp;</p>
				<div class="alert alert-block alert-info ph-w80">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<?php echo $class::getLinks(); ?>
				</div>
			</div>
		</div>

		<div class="ph-cpanel-right">
			<div class="well">

                <?php echo '<div class="ph-cpanel-logo">'.JHtml::_('image', 'media/com_phocapdf/images/administrator/logo-phoca-pdf.png', 'Phoca.cz') . '</div>'; ?>

				<div style="float:right;margin:10px;"><?php echo JHTML::_('image', $this->t['i'] . 'logo-phoca.png', 'Phoca.cz' );?></div><?php
echo '<h3>'.  JText::_($this->t['l'] . '_VERSION').'</h3>'
.'<p>'.  $this->t['version'] .'</p>';
echo '<h3>'.  JText::_($this->t['l'] . '_COPYRIGHT').'</h3>'
.'<p>© 2007 - '.  date("Y"). ' Jan Pavelka</p>'
.'<p><a href="https://www.phoca.cz/" target="_blank">www.phoca.cz</a></p>';
echo '<h3>'.  JText::_($this->t['l'] . '_LICENSE').'</h3>'
.'<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>';
echo '<h3>'.  JText::_($this->t['l'] . '_TRANSLATION').': '. JText::_($this->t['l'] . '_TRANSLATION_LANGUAGE_TAG').'</h3>'
.'<p>© 2007 - '.  date("Y"). ' '. JText::_($this->t['l'] . '_TRANSLATER'). '</p>'
.'<p>'.JText::_($this->t['l'] . '_TRANSLATION_SUPPORT_URL').'</p>';

echo '<div style="border-top:1px solid #c2c2c2"></div><p>&nbsp;</p>'
.'<div class="btn-group" style="float:left;"><a class="btn btn-large btn-primary" href="https://www.phoca.cz/version/index.php?phocapdf='.  $this->t['version'] .'" target="_blank"><i class="icon-loop icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'] . '_CHECK_FOR_UPDATE') .'</a></div>';

echo '<div class="clearfix" style="margin-bottom: 5px;"></div>';

echo '<div class="btn-group" style="float:left;"><a class="btn btn-info" href="https://www.phoca.cz/phocapdf-plugins" target="_blank"><i class="icon-share icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'] . '_CHECK_FOR_AVAILABLE_PLUGINS') .'</a></div>';

echo '<div class="btn-group" style="float:left;"><a class="btn btn-info" href="https://www.phoca.cz/phocapdf-fonts" target="_blank"><i class="icon-share icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'] . '_CHECK_FOR_AVAILABLE_FONTS') .'</a></div>';
echo '<div class="clearfix"></div>';

echo '<div style="float:right; margin: 20px 10px 5px 10px"><a href="https://www.phoca.cz/" target="_blank">'.JHTML::_('image', $this->t['i'] . 'logo.png', 'Phoca.cz' ).'</a></div>';

echo '<div class="clearfix"></div>';

			?></div>
		</div>
	</div>
	<input type="hidden" name="option" value="<?php echo $this->t['c'] ?>" />
	<input type="hidden" name="view" value="<?php echo $this->t['c'] ?>cp" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
