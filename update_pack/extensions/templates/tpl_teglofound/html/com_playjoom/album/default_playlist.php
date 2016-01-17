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
 * @copyright Copyright (C) 2010-2012 by www.teglo.info
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @date $Date: 2013-09-08 14:20:12 +0200 (So, 08 Sep 2013) $
 * @revision $Revision: 829 $
 * @author $Author: toto $
 * @headurl $HeadURL: http://dev.teglo.info/svn/playjoom/components/com_playjoom/views/album/tmpl/default_playlist.php $
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('formbehavior.chosen', 'select');

$albumsting = JRequest::getVar('album');
$artiststing = JRequest::getVar('artist');
$link = 'index.php?option=com_playjoom&view=playlist&source=album&name='.$albumsting.'&artist='.$artiststing;
$linkwithorder = 'index.php?option=com_playjoom&view=playlist&source=album&orderplaylist=RAND()&name='.$albumsting.'&artist='.$artiststing;

echo '<fieldset class="batch">';

	echo '<legend>'.JText::_('COM_PLAYJOOM_PLAYLIST_LABEL_PLAYLIST').'</legend>';
	
	echo '<div class="directplay">';
	    echo '<a href="'.$link.'" title="'.JText::_('COM_PLAYJOOM_PLAYLIST_CONTINUE_PLAYLIST').'" class="small button" target="_blank">'.JText::_('COM_PLAYJOOM_PLAYLIST_PLAY_ALL').'</a>';
	    echo '<a href="'.$linkwithorder.'" title="'.JText::_('COM_PLAYJOOM_PLAYLIST_CONTINUE_PLAYLIST').'" class="small button" target="_blank">'.JText::_('COM_PLAYJOOM_PLAYLIST_PLAY_RAND').'</a>';
	echo '</div>';
	
	echo '<form action="'.JRoute::_('index.php?option=com_playjoom&view=playlist').'" method="post" name="playlistForm" id="playlistForm">';
	     echo '<p>'.JText::_('COM_PLAYJOOM_PLAYLIST_DOWNLOAD_PLAYLIST').'</p>';
	     
	     echo '<select name="attachment_playlist" class="PJ-filtermenu" id="playlisttype">';
	         echo '<option value="">'.JText::_('COM_PLAYJOOM_PLAYLIST_SELECT_TYPE').'</option>';
	         echo '<option value="m3u">M3U</option>';
	         echo '<option value="pls">PLS</option>';	     
	         echo '<option value="wpl">WPL</option>';
	         echo '<option value="xspf">XSPF</option>';
	     echo '</select>';
	    
	     echo '<select name="orderplaylist" class="PJ-filtermenu" id="ordertype" onchange="this.form.submit()">';
	         echo '<option value="">'.JText::_('COM_PLAYJOOM_PLAYLIST_SELECT_ORDER').'</option>';
	         echo '<option value="a.tracknumber">'.JText::_('COM_PLAYJOOM_PLAYLIST_ORDER_TYPE_TRACKNO').'</option>';
	         echo '<option value="RAND()">'.JText::_('COM_PLAYJOOM_PLAYLIST_ORDER_TYPE_RAND').'</option>';
	         echo '<option value="a.title">'.JText::_('COM_PLAYJOOM_PLAYLIST_ORDER_TYPE_TITLE').'</option>';
	         echo '<option value="a.hits">'.JText::_('COM_PLAYJOOM_PLAYLIST_ORDER_TYPE_HITS').'</option>';
	     echo '</select>';
	     
	     echo '<input type="hidden" name="name" value="'.$albumsting.'" />';
	     echo '<input type="hidden" name="artist" value="'.$artiststing.'" />';
	     echo '<input type="hidden" name="source" value="album" />';
	     echo '<input type="hidden" name="disposition" value="attachment" />';
	     
	echo '</form>';

echo '</fieldset>';
if (!empty($this->plitems)) {
	
	echo '<fieldset class="batch">';

         echo '<legend>'.JText::_('COM_PLAYJOOM_PLAYLIST_LABEL_PLAYLIST_ATTACH').'</legend>';
         //Attachment lists
         if (!empty($this->plitems)) {	          
             echo '<ul class="circle">';
              
                 foreach($this->plitems as $i => $item) {
                    //create link for form
                    $PLlink = 'index.php?option=com_playjoom&view=playlist&source=playlist&listid='.$item->id.'&name='.base64_encode($item->name);
	                echo '<li><a href="'.$PLlink.'" title="'.JText::_('COM_PLAYJOOM_PLAYLIST_CONTINUE_PLAYLIST').'" target="_blank">'.$item->name.'</a></li>';
                 }  
             echo '</ul>';
         }
    echo '</fieldset>';
}