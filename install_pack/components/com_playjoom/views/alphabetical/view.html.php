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
 * @copyright Copyright (C) 2010-2013 by www.teglo.info
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @date $Date$
 * @revision $Revision$
 * @author $Author$
 * @headurl $HeadURL$
 */

defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport( 'joomla.application.component.helper' );


/**
 * HTML View class for the PlayJoom Component
 */
class PlayJoomViewAlphabetical extends JViewLegacy
{
        // Overwriting JView display method
        function display($tpl = null) {
                // Get data from the model
                $items       = $this->get('Items');

                $pagination  = $this->get('Pagination');

                //Get setting values from xml file
                $app		= JFactory::getApplication();
                $params		= $app->getParams();

                // Assign data to the view
                $this->items = $items;
                $this->pagination = $pagination;

                //For filter and ordering function
                $this->state = $this->get('State');
                $this->authors = $this->get('Authors');

                $this->assignRef('params',		$params);

                // add style sheet
                if ($this->params->get('css_type', 'pj_css') == 'pj_css') {
                	$document	= JFactory::getDocument();
                	$document->addStyleSheet(JURI::base(true).'/components/com_playjoom/assets/css/filter.css');
                }

                $this->_prepareDocument($params);

                parent::display($tpl);
        }

        /**
         * Prepares the document
         */
        protected function _prepareDocument($params) {

        	$app	  = JFactory::getApplication();
        	$document = JFactory::getDocument();

        	// add style sheet
            if ($this->params->get('css_type', 'pj_css') == 'pj_css') {
                $document->addStyleSheet(JURI::base(true).'/components/com_playjoom/assets/css/filter.css');
                $document->addStyleSheet(JURI::root(true).'/components/com_playjoom/assets/css/playlist_view.css');
            }

        	//Pathway settings
        	if (JRequest::getVar('LetterForAlphabetical')) {
        		$pathway = $app->getPathway();
        	    $pathway->addItem(JRequest::getVar('LetterForAlphabetical'), PlayJoomHelperRoute::getPJlink('alphabetical', '&LetterForAlphabetical='.JRequest::getVar('LetterForAlphabetical')));
        	}

        	//Set Page title
        	$this->document->setTitle($app->getCfg('sitename').' - '.JRequest::getVar('LetterForAlphabetical'));
        }
}
