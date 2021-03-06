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
 * @date $Date: 2011-05-19 21:40:50 +0200 (Do, 19 Mai 2011) $
 * @revision $Revision: 187 $
 * @author $Author: toto $
 * @headurl $HeadURL: http://localhost/repos/playjoom/components/com_playjoom/views/infoabout/tmpl/default.php $
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

// add style sheet
if ($this->params->get('css_type', 'pj_css') == 'pj_css') {
	$document	= & JFactory::getDocument();
    $document->addStyleSheet(JURI::base(true).'/components/com_playjoom/assets/css/infoabout_view.css');
}

//Set variablen
$type = JRequest::getVar('type');
$about = base64_decode(JRequest::getVar('about'));
$source = JRequest::getVar('source');

echo '<fieldset class="infoabout">';

    switch ($type) 
    {
	case "album" :
		echo '<h2>'.JText::_('COM_PLAYJOOM_INFOABOUT_TITLE').' '.$this->lastFMRequest->album->name.'</h2>';  
		echo '<ul class="infoabout">';
		echo '<li>'.$this->lastFMRequest->album->wiki->content.'</li>';
		echo '</ul>';
    break;
    	    
    case "artist" :
    	$image = $this->lastFMRequest->artist->image;
    	echo '<h2>'.JText::_('COM_PLAYJOOM_INFOABOUT_TITLE').' '.$this->lastFMRequest->artist->name.'</h2>'; 
    	echo '<ul class="infoabout">';
    	if ($image[4] != ''){
    		echo '<li><img src="'.$image[4].'" class="artist_image" alt="Artist image of '.$this->lastFMRequest->artist->name.'" /></li>';
    	}
    	echo '<li>'.$this->lastFMRequest->artist->bio->content.'</li>';
    	echo '</ul>';
    break;
    	    
    case "genre" :
    	echo '<h2>'.$this->info->title.'</h2>';
    	echo $this->info->description;
    break;
    		
    case "track" :
    	echo $this->info->description;
    break;
    	    
    default:
    	echo 'No Information about it.';    		
    }
    
    echo '</ul>';


//Out the footrow
echo '<br />';
echo '<br />';
echo '<center><button type="button" onclick="window.parent.SqueezeBox.close();">'.JText::_('COM_PLAYJOOM_ADDTRACK_LABEL_CLOSE').'</button></center>';
echo '</fieldset>';