<?php
/**
 * Contains the model methods for to get the list of albums in PlayJoom frontend.
 *
 * PlayJoom and the basic package Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and details.
 *
 * @package PlayJoom.Site
 * @subpackage models.tracks
 * @link http://playjoom.teglo.info
 * @copyright Copyright (C) 2010-2012 by www.teglo.info. All rights reserved.
 * @license	GNU/GPL, see LICENSE.php
 * @PlayJoom Component
 * @date $Date: 2012-04-08 14:07:01 +0200 (So, 08. Apr 2012) $
 * @revision $Revision: 455 $
 * @author $Author: toto $
 * @headurl $HeadURL: http://dev.teglo.info/svn/playjoom/components/com_playjoom/helpers/playjoom.php $
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of PlayJoom artists.
 *
 * @package PlayJoom.Site
 * @subpackage models.tracks
 * @since		0.9.482
 */
class PlayJoomModelTracks extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
    public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'artist', 'a.artist',
			    'album', 'a.album',
			    'year', 'a.year',
				'title', 'a.title',
				'catid', 'a.catid', 'category_title',
				'access', 'a.access', 'access_level',
			    'addby', 'a.add_by', 'user'
			);
		}

		parent::__construct($config);
	}

   /**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
    protected function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();


		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$artist = $app->getUserStateFromRequest($this->context.'.filter.artist', 'filter_artist');
		$this->setState('filter.artist', $artist);

		$album = $app->getUserStateFromRequest($this->context.'.filter.album', 'filter_album', '');
		$this->setState('filter.album', $album);

		$year = $app->getUserStateFromRequest($this->context.'.filter.year', 'filter_year', '');
		$this->setState('filter.year', $year);

		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		// Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        // process show_noauth parameter
        if (!$params->get('show_noauth', 1)) {
        	$this->setState('filter.access', true);
        }
        else {
        	$this->setState('filter.access', false);
        }

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '') {
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.artist');
		$id	.= ':'.$this->getState('filter.year');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.user_id');

		return parent::getStoreId($id);
	}

	/**
	 * Get the master query for retrieving a list of artists subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
    protected function getListQuery() {

		//For getting the xml parameters
		$app = JFactory::getApplication();
        $params		= $app->getParams();

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select($this->getState('list.select','a.id, a.artist, a.album, a.title, a.year, a.pathatlocal, a.file, a.catid, a.access, a.add_by, a.metadesc, a.metakey'));
		$query->from('#__jpaudiotracks AS a');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Implement View Level Access
		if (!$user->authorise('core.admin')
		 && !$params->get('show_noauth', 1)) {

			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$groups = '0,'.$groups;
			$query->where('a.access IN ('.$groups.')');
		}

        // Filter by Artist / Band.
		if ($artist = $this->getState('filter.artist')) {
			$query->where('a.artist = "' . $artist .'"');
		}

		// Filter by Category.
		if ($category_id = $this->getState('filter.category_id')) {
			$query->where('a.catid = ' . $category_id);
		}

		// Filter by Category.
		if ($user_id = $this->getState('filter.user_id')) {
			$query->where('a.add_by = ' . $user_id);
		}

		// Filter by Album.
		if ($album = $this->getState('filter.album')) {
			$query->where('a.album = "' . $album .'"');
		}

		// Filter by Year.
		if ($year = $this->getState('filter.year')) {
			$query->where('a.year = ' . $year);
		}

	    //Filtering by user
        if (JAccess::check($user->get('id'), 'core.admin') != 1) {

        	//Get user id
        	$users = $user->get('id');

        	$userCheck = $params->get('show_all_users', 1);
        	$userCheck = (int)$userCheck + $params->get('show_nobody', 1);

        	if ($userCheck == 1) {

        	    if ($params->get('show_all_users', 1)) {
        		   $query->where('add_by >= 1');
        	    }

        	    if ($params->get('show_nobody', 1)) {
        		$users = '0,'.$users;
        		  $query->where('add_by IN ('.$users.')');
        	    }
        	}
        	elseif ($userCheck == 0) {
        		$query->where('add_by = '.$users.'');
        	}
        }

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.metakey LIKE '.$search.' OR a.metadesc LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		//Special ordering for the colum category
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}

		// Add the list ordering clause.
		//$query->order($db->getEscaped($this->getState('list.ordering', 'a.title')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		$query->order($this->getState('list.ordering', 'a.ordering').' '.$this->getState('list.direction', 'ASC'));

		return $query;
	}

	/**
	 * Method for to get a list of valid artists for the filter menu
	 *
	 * @return  JTable  A database object
	 * @since   0.9.460
	 */
	public function getFilterOptionsArtists() {

		//Get User Objects
		$user	= JFactory::getUser();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('artist As value, artist As text');
		$query->order('a.artist');
		$query->group('a.artist');

		// Filter by Artist / Band.
		if ($artist = $this->getState('filter.artist')) {
			$query->where('a.artist = "' . $artist .'"');
		}

		// Filter by Category.
		if ($category_id = $this->getState('filter.category_id')) {
			$query->where('a.catid = ' . $category_id);
		}

		// Filter by Category.
		if ($user_id = $this->getState('filter.user_id')) {
			$query->where('a.add_by = ' . $user_id);
		}

		// Filter by Album.
		if ($album = $this->getState('filter.album')) {
			$query->where('a.album = "' . $album .'"');
		}

		// Filter by Year.
		if ($year = $this->getState('filter.year')) {
			$query->where('a.year = ' . $year);
		}

		// Filter by User.
		if (JAccess::check($user->get('id'), 'core.admin') != 1) {

			$query->where('a.add_by = '.$user->get('id'));
		}

		$query->from('#__jpaudiotracks AS a');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}

	/**
	 * Method for to get a list of valid years for the filter menu
	 *
	 * @return  JTable  A database object
	 * @since   0.9.460
	 */
	public function getFilterOptionsYears() {

		//Get User Objects
		$user	= JFactory::getUser();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('year As value, year As text');
		$query->order('a.year DESC');
		$query->group('a.year DESC');

		// Filter by Artist / Band.
		if ($artist = $this->getState('filter.artist')) {
			$query->where('a.artist = "' . $artist .'"');
		}

		// Filter by Category.
		if ($category_id = $this->getState('filter.category_id')) {
			$query->where('a.catid = ' . $category_id);
		}

		// Filter by Category.
		if ($user_id = $this->getState('filter.user_id')) {
			$query->where('a.add_by = ' . $user_id);
		}

		// Filter by Album.
		if ($album = $this->getState('filter.album')) {
			$query->where('a.album = "' . $album .'"');
		}

		// Filter by Year.
		if ($year = $this->getState('filter.year')) {
			$query->where('a.year = ' . $year);
		}

		// Filter by User.
		if (JAccess::check($user->get('id'), 'core.admin') != 1) {

			$query->where('a.add_by = '.$user->get('id'));
		}

		$query->from('#__jpaudiotracks AS a');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}

	/**
	 * Method for to get a list of valid genres for the filter menu
	 *
	 * @return  JTable  A database object
	 * @since   0.9.460
	 */
	public function getFilterOptionsGenres() {

		//Get User Objects
		$user	= JFactory::getUser();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('catid As value');
		$query->group('a.catid');

		// Join over the categories.
		$query->select('c.title AS text');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Filter by Artist / Band.
		if ($artist = $this->getState('filter.artist')) {
			$query->where('a.artist = "' . $artist .'"');
		}

		// Filter by Category.
		if ($category_id = $this->getState('filter.category_id')) {
			$query->where('a.catid = ' . $category_id);
		}

		// Filter by Category.
		if ($user_id = $this->getState('filter.user_id')) {
			$query->where('a.add_by = ' . $user_id);
		}

		// Filter by Album.
		if ($album = $this->getState('filter.album')) {
			$query->where('a.album = "' . $album .'"');
		}

		// Filter by Year.
		if ($year = $this->getState('filter.year')) {
			$query->where('a.year = ' . $year);
		}

		// Filter by User.
		if (JAccess::check($user->get('id'), 'core.admin') != 1) {

			$query->where('a.add_by = '.$user->get('id'));
		}

		$query->from('#__jpaudiotracks AS a');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}

	/**
	 * Method for to get a list of valid albums for the filter menu
	 *
	 * @return  JTable  A database object
	 * @since   0.9.460
	 */
	public function getFilterOptionsAlbums() {

		//Get User Objects
		$user	= JFactory::getUser();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('album As value, album As text');
		$query->order('a.album');
		$query->group('a.album');

		// Filter by Artist / Band.
		if ($artist = $this->getState('filter.artist')) {
			$query->where('a.artist = "' . $artist .'"');
		}

		// Filter by Category.
		if ($category_id = $this->getState('filter.category_id')) {
			$query->where('a.catid = ' . $category_id);
		}

		// Filter by Category.
		if ($user_id = $this->getState('filter.user_id')) {
			$query->where('a.add_by = ' . $user_id);
		}

		// Filter by Album.
		if ($album = $this->getState('filter.album')) {
			$query->where('a.album = "' . $album .'"');
		}

		// Filter by Year.
		if ($year = $this->getState('filter.year')) {
			$query->where('a.year = ' . $year);
		}

		// Filter by User.
		if (JAccess::check($user->get('id'), 'core.admin') != 1) {

			$query->where('a.add_by = '.$user->get('id'));
		}

		$query->from('#__jpaudiotracks AS a');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}
}