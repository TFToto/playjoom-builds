<?php
/**
 * @package Joomla 1.6.x
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 *
 * @PlayJoom Component
 * @copyright Copyright (C) 2010-2011 by www.teglo.info
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @date $Date: 2013-09-10 19:13:25 +0200 (Di, 10 Sep 2013) $
 * @revision $Revision: 842 $
 * @author $Author: toto $
 * @headurl $HeadURL: http://dev.teglo.info/svn/playjoom/components/com_playjoom/views/adminplaylist/tmpl/deltrack_toolbar.php $
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// add style sheet
if ($this->params->get('css_type', 'pj_css') == 'pj_css') {
	$document	= JFactory::getDocument();
    $document->addStyleSheet(JURI::base(true).'/components/com_playjoom/assets/css/toolbar.css');
}

//Load JavaScripts for light box and for submit script
JHtml::_('behavior.modal');
?>

                          <a href="#" onclick="javascript:submitbutton()" class="small button"><i class="fa fa-trash-o"></i><?php  echo JText::_('COM_PLAYJOOM_TRACK_DELETE'); ?></a>
                          <a href="index.php?option=com_playjoom&view=adminplaylist&listid=<?php echo JRequest::getVar('listid');?>&Itemid=<?php echo JRequest::getVar('Itemid');?>" class="small button"><i class="fa fa-times"></i><?php echo JText::_('COM_PLAYJOOM_PLAYLISTS_CANCEL'); ?></a>

		    <div class="pagetitle icon-48-playjoom"><h2><?php echo JText::_('COM_PLAYJOOM_PLAYJOOM_DEL'); ?></h2><?php echo  JText::_('COM_PLAYJOOM_PLAYJOOM_SURE')?></div>