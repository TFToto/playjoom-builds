<?php
/**
 * Contains the controller method for file media.
 * 
 * PlayJoom and the basic package Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and details. 
 * 
 * @package PlayJoom.Admin
 * @subpackage controller.file.json
 * @link http://playjoom.teglo.info
 * @copyright Copyright (C) 2010-2013 by www.teglo.info. All rights reserved.
 * @license	GNU/GPL, see LICENSE.php
 * @PlayJoom Component
 * @date $Date$
 * @revision $Revision$
 * @author $Author$
 * @headurl $HeadURL$
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * File Media Controller
 *
 * @package		PlayJoom.Admin
 * @subpackage	    controller.file.json
 * @since		1.6
 */
class PlayJoomControllerFile extends JControllerLegacy {
	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload() {
		
		$dispatcher	= JDispatcher::getInstance();
		
		$params = JComponentHelper::getParams('com_playjoom');
		
		// Check for request forgeries
		if (!JSession::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user  = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		JLog::addLogger(array('text_file' => 'upload.error.php'), JLog::ALL, array('upload'));

        // Get some data from the request
		$file   = JRequest::getVar('Filedata', '', 'files', 'array');
		$folder = JRequest::getVar('folder', '', 'path');
		$return = $input->post->get('return-url', null, 'base64');		
		
		$dispatcher->trigger('onEventLogging', array(array('method' => __METHOD__.":".__LINE__, 'message' => 'Start uploading file.json: '.$folder.DIRECTORY_SEPARATOR.$file['name'], 'priority' => JLog::INFO, 'section' => 'admin')));
		
		if (
			$_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 100) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('memory_limit'))* 1024 * 1024
		)
		{
			$response = array(
					'status' => '0',
					'error' => JText::_('COM_PLAYJOOM_ERROR_WARNFILETOOLARGE')
			);
			echo json_encode($response);
			return;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = JPath::clean(PLAYJOOM_BASE_PATH . '/' . $folder . '/' . strtolower($file['name']));
			$allowableExtensions = $params->get('upload_audio_extensions', 'mp3,wav,flac');
			
			if (!PlayJoomMediaHelper::canUpload($file, $err, $allowableExtensions))
			{
				JLog::add('Invalid: ' . $filepath . ': ' . $err, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JEventDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_playjoom.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				JLog::add('Errors before save: ' . $filepath . ' : ' . implode(', ', $object_file->getErrors()), JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::plural('COM_PLAYJOOM_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				echo json_encode($response);
				return;
			}

			if (JFile::exists($filepath))
			{
				// File exists
				JLog::add('File exists: ' . $filepath . ' by user_id ' . $user->id, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_PLAYJOOM_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			}
			elseif (!$user->authorise('core.create', 'com_playjoom'))
			{
				// File does not exist and user is not authorised to create
				JLog::add('Create not permitted: ' . $filepath . ' by user_id ' . $user->id, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_PLAYJOOM_ERROR_CREATE_NOT_PERMITTED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JLog::add('Error on upload: ' . $filepath, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_PLAYJOOM_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_playjoom.file', &$object_file, true));
				JLog::add($folder, JLog::INFO, 'upload');
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_PLAYJOOM_UPLOAD_COMPLETE', substr($file['filepath'], strlen(PLAYJOOM_BASE_PATH)))
				);
				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_PLAYJOOM_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}
}
