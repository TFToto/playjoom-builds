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
 * @date $Date$
 * @revision $Revision$
 * @author $Author$
 * @headurl $HeadURL$
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// add style sheet
if ($this->params->get('css_type', 'pj_css') == 'pj_css') {
	$document	= & JFactory::getDocument();
    $document->addStyleSheet(JURI::base(true).'/components/com_playjoom/assets/css/toolbar.css');
}

//Load JavaScripts for light box and for submit script
JHtml::_('behavior.modal'); 
?>
   
			<div class="toolbar-box">			    
				    <div class="toolbar-list" id="toolbar">
                       <ul>
                          <li class="button" id="toolbar-delete"><a href="#" onclick="javascript:submitbutton()" class="toolbar"><span class="icon-32-delete"></span><?php  echo JText::_('COM_PLAYJOOM_TRACK_DELETE'); ?></a></li>
                          <li class="button" id="toolbar-cancel"><a href="index.php?option=com_playjoom&view=adminplaylist&listid=<?php echo JRequest::getVar('listid');?>&Itemid=<?php echo JRequest::getVar('Itemid');?>" class="toolbar"><span class="icon-32-cancel"></span><?php echo JText::_('COM_PLAYJOOM_PLAYLISTS_CANCEL'); ?></a></li>
                       </ul>
                    </div>             
           
		    <div class="pagetitle icon-48-playjoom"><h2><?php echo JText::_('COM_PLAYJOOM_PLAYJOOM_DEL'); ?></h2><?php echo  JText::_('COM_PLAYJOOM_PLAYJOOM_SURE')?></div>
            </div>  