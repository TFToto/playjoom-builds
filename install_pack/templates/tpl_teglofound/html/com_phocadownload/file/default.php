<?php
defined('_JEXEC') or die('Restricted access'); 

$pdFile = null;

if ( $this->t['p']->get( 'show_page_heading' ) ) { 
	echo '<h1>'. $this->escape($this->t['p']->get('page_heading')) . '</h1>';
}

echo '<ul class="pricing-table">';

if (!empty($this->category[0])) {
	if ($this->t['display_up_icon'] == 1 && $this->t['tmplr'] == 0) {
		
		if (isset($this->category[0]->id)) {
			if ($this->category[0]->id > 0) {
				$linkUp = JRoute::_(PhocaDownloadRoute::getCategoryRoute($this->category[0]->id, $this->category[0]->alias));
				$linkUpText = $this->category[0]->title;
			} else {
				$linkUp 	= '#';
				$linkUpText = ''; 
			}
			echo '<div class="pdtop"><a title="'.$linkUpText.'" href="'. $linkUp.'" ><i class="fa fa-undo"></i></a></div>';
		}
	}
} 

if (!empty($this->file[0])) {
	$v = $this->file[0];
	
	// USER RIGHT - Access of categories (if file is included in some not accessed category) - - - - -
	// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
	$rightDisplay	= 0;
	if (!empty($this->category[0])) {
		$rightDisplay = PhocaDownloadAccess::getUserRight('accessuserid', $v->cataccessuserid, $v->cataccess, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
	}
	// - - - - - - - - - - - - - - - - - - - - - -
	
	if ($rightDisplay == 1) {
	
		$l = new PhocaDownloadLayout();
	
		echo '<li class="title">'.$l->getName($v->title, $v->filename, 1). '</li>';
		
		
// =====================================================================================		
// BEGIN LAYOUT AREA
// =====================================================================================
		
		// Is this direct menu link to File View
		$directFv 	= 0;
		$app		= JFactory::getApplication();
		$itemId 	= $app->input->get('Itemid', 0, 'int');
		$menu		= $app->getMenu();
		$item		= $menu->getItem($itemId);
		if (isset($item->query['view']) && $item->query['view'] == 'file') {
			$directFv = 1;
		}
		// End direct menu link to File View

		if ((int)$this->t['display_file_view'] == 1
		|| (int)$this->t['display_file_view'] == 2
		|| (int)$v->confirm_license > 0
		|| (int)$this->t['display_detail'] == 2
		|| (int)$directFv == 1) {
					
			$pdTitle = '';
			if ($v->title != '') {
				$pdTitle .= '<div class="pd-title">'.$v->title.'</div>';
			}
					
			$pdImage = '';
			if ($v->image_download != '') {
				$pdImage .= '<div class="pd-image">'.$l->getImageDownload($v->image_download).'</div>';		
			}
			
			$pdVideo = '';
			$pdVideo = $l->displayVideo($v->video_filename, 1);
					
			if ($v->filename != '') {
				$imageFileName = $l->getImageFileName($v->image_filename, $v->filename);
				
				//$pdFile = '<div class="pd-filenamebox">';
				if ($this->t['filename_or_name'] == 'filenametitle') {
					$pdFile = '<li class="bullet-item">'. $v->title . '</li>';
				}
				
				$pdFile .= '<div class="pd-filename">'. $imageFileName['filenamethumb']
					. '<div class="pd-document'.$this->t['file_icon_size'].'" '
					. $imageFileName['filenamestyle'].'>';
				
				$pdFile .= '<li class="bullet-item">';
				$pdFile .= $l->getName($v->title, $v->filename);
				$pdFile .= '</li>';
				
				$pdFile .= PhocaDownloadRenderFront::displayNewIcon($v->date, $this->t['displaynew']);
				$pdFile .= PhocaDownloadRenderFront::displayHotIcon($v->hits, $this->t['displayhot']);
				
				//Specific icons
				if (isset($v->image_filename_spec1) && $v->image_filename_spec1 != '') {
					$pdFile .= '<div class="pd-float">'.$l->getImageDownload($v->image_filename_spec1).'</div>';
				} 
				if (isset($v->image_filename_spec2) && $v->image_filename_spec2 != '') {
					$pdFile .= '<div class="pd-float">'.$l->getImageDownload($v->image_filename_spec2).'</div>';
				} 
				
				$pdFile .= '</div></div>' . "\n";
			}
					
			$pdFileSize = '';
			$fileSize = $l->getFilesize($v->filename);
			if ($fileSize != '') {
				$pdFileSize .= '<li class="bullet-item"><i class="fa fa-hdd-o"></i> '.JText::_('COM_PHOCADOWNLOAD_FILESIZE').': '.$fileSize.'</li>';
			}
					
			$pdVersion = '';
			if ($v->version != '') {
				$pdVersion .= '<li class="bullet-item"><i class="fa fa-info-circle"></i> '.JText::_('COM_PHOCADOWNLOAD_VERSION').': '.$v->version.'</li>';
			}
					
			$pdLicense = '';
			if ($v->license != '') {
				if ($v->license_url != '') {
					$pdLicense .= '<li class="bullet-item"><i class="fa fa-pencil-square-o"></i> '.JText::_('COM_PHOCADOWNLOAD_LICENSE').': <a href="'.$v->license_url.'" target="_blank">'.$v->license.'</a></li>';
				} else {
					$pdLicense .= '<li class="bullet-item"><i class="fa fa-pencil-square-o"></i> '.JText::_('COM_PHOCADOWNLOAD_LICENSE').': '.$v->license.'</li>';
				}
			}
					
			$pdAuthor = '';
			if ($v->author != '') {
				if ($v->author_url != '') {
					$pdAuthor .= '<li class="bullet-item"><i class="fa fa-user"></i> '.JText::_('COM_PHOCADOWNLOAD_AUTHOR').': <a href="'.$v->author_url.'" target="_blank">'.$v->author.'</a></li>';
				} else {
					$pdAuthor .= '<li class="bullet-item"><i class="fa fa-user"></i> '.JText::_('COM_PHOCADOWNLOAD_AUTHOR').': '.$v->author.'</li>';
				}
			}
			
			$pdAuthorEmail = '';
			if ($v->author_email != '') {
				$pdAuthorEmail .= '<li class="bullet-item"><i class="fa fa-envelope-o"></i> '.JText::_('COM_PHOCADOWNLOAD_EMAIL').': '. $l->getProtectEmail($v->author_email).'</div>';
			}
					
			$pdFileDate = '';
			$fileDate = $l->getFileDate($v->filename, $v->date);
			if ($fileDate != '') {
				$pdFileDate .= '<li class="bullet-item"><i class="fa fa-calendar"></i> '.JText::_('COM_PHOCADOWNLOAD_DATE').': '.$fileDate.'</li>';
			}
					
			$pdDownloads = '';
			if ($this->t['display_downloads'] == 1) {
				$pdDownloads .= '<li class="bullet-item"><i class="fa fa-bar-chart-o"></i> '.JText::_('COM_PHOCADOWNLOAD_DOWNLOADS').': '.$v->hits.' x</li>';
			}
					
			$pdDescription = '';
			if ($l->isValueEditor($v->description)) {
				$pdDescription .= $v->description;
			}
			
			$pdFeatures = '';
			if ($l->isValueEditor($v->features)) {
				$pdFeatures .= $v->features;
			}
					
			$pdChangelog = '';
			if ($l->isValueEditor($v->changelog)) {
				$pdChangelog .= $v->changelog;
			}
			
			$pdNotes = '';
			if ($l->isValueEditor($v->notes)) {
				$pdNotes .= '<div class="pd-notes-txt">'.JText::_('COM_PHOCADOWNLOAD_NOTES').'</div>';
				$pdNotes .= '<div class="pd-notes">'.$v->notes.'</div>';
			}
					

			// pdmirrorlink1
			$pdMirrorLink1 = '';
			$mirrorOutput1 = PhocaDownloadRenderFront::displayMirrorLinks(1, $v->mirror1link, $v->mirror1title, $v->mirror1target);
			if ($mirrorOutput1 != '') {
				
				if ($this->t['display_mirror_links'] == 4 || $this->t['display_mirror_links'] == 6) {
					$classMirror = 'pd-button-mirror1';
				} else {
					$classMirror = 'pd-mirror';
				}
				
				$pdMirrorLink1 = '<div class="'.$classMirror.'">'.$mirrorOutput1.'</div>';
			}

			// pdmirrorlink2
			$pdMirrorLink2 = '';
			$mirrorOutput2 = PhocaDownloadRenderFront::displayMirrorLinks(1, $v->mirror2link, $v->mirror2title, $v->mirror2target);
			if ($mirrorOutput2 != '') {
				if ($this->t['display_mirror_links'] == 4 || $this->t['display_mirror_links'] == 6) {
					$classMirror = 'pd-button-mirror2';
				} else {
					$classMirror = 'pd-mirror';
				}
			
				$pdMirrorLink2 = '<div class="'.$classMirror.'">'.$mirrorOutput2.'</div>';
			}
			
			// pdreportlink
			$pdReportLink = PhocaDownloadRenderFront::displayReportLink(1, $v->title);

			
			// pdrating
			$pdRating 	= PhocaDownloadRate::renderRateFile($v->id, $this->t['display_rating_file']);
			
			// pdtags
			$pdTags = '';
			if ($this->t['display_tags_links'] == 2 || $this->t['display_tags_links'] == 3) {
				if ($l->displayTags($v->id) != '') {
					$pdTags .= $l->displayTags($v->id);
				}
			
			}

			
			// ---------------------------------------------------
			//Convert
			// ---------------------------------------------------
			if ($this->t['display_specific_layout'] == 0) {

				//echo $pdTitle;
				echo $pdImage;
				echo $pdFile;
				echo $pdFileSize;
				echo $pdVersion;
				echo $pdLicense;
				echo $pdAuthor;
				echo $pdAuthorEmail;
				echo $pdFileDate;
				echo $pdDownloads;
				
				echo '</ul>';
				
				echo '<div class="section-container auto" data-section>';
					if($pdDescription) {
						echo '<section class="active">';
							echo '<b class="title" data-section-title><a href="#panel1">'.JText::_( 'COM_PHOCADOWNLOAD_DESCRIPTION' ).'</a></b>';
							echo '<div class="content" data-section-content>';
								echo $pdDescription;
							echo '</div>';
						echo '</section>';
					}
					if($pdFeatures) {
						echo '<section>';
							echo '<b class="title" data-section-title><a href="#panel2">'.JText::_( 'COM_PHOCADOWNLOAD_FEATURES' ).'</a></b>';
							echo '<div class="content" data-section-content>';
								echo $pdFeatures;
							echo '</div>';
						echo '</section>';
					}
					if($pdChangelog) {
						echo '<section>';
							echo '<b class="title" data-section-title><a href="#panel3">'.JText::_( 'COM_PHOCADOWNLOAD_CHANGELOG' ).'</a></b>';
							echo '<div class="content" data-section-content>';
								echo $pdChangelog;
							echo '</div>';
						echo '</section>';
					}
					echo $pdNotes;
				echo '</div>';
				
				
				if ($this->t['display_mirror_links'] == 5 || $this->t['display_mirror_links'] == 6) {
				echo '<div class="pd-buttons">'.$pdMirrorLink2.'</div>';
				echo '<div class="pd-buttons">'.$pdMirrorLink1.'</div>';
				} else if ($this->t['display_mirror_links'] == 2 || $this->t['display_mirror_links'] == 3) {
					echo '<div class="pd-mirrors">'.$pdMirrorLink2.$pdMirrorLink1.'</div>';
				}
				
				echo '<div class="pd-report">'.$pdReportLink.'</div>';
				echo '<div class="pd-rating">'.$pdRating.'</div>';
				echo '<div class="pd-tags">'.$pdTags.'</div>';
				echo '<div class="pd-video">'.$pdVideo.'</div>';
				echo '<div class="pd-cb"></div>';
			} else {
			
				$fileLayout 		= PhocaDownloadSettings::getLayoutText('file');
				$fileLayoutParams 	= PhocaDownloadSettings::getLayoutParams('file');
				
				$replace	= array($pdTitle, $pdImage, $pdFile, $pdFileSize, $pdVersion, $pdLicense, $pdAuthor, $pdAuthorEmail, $pdFileDate, $pdDownloads, $pdDescription, $pdFeatures, $pdChangelog, $pdNotes, $pdMirrorLink1, $pdMirrorLink2, $pdReportLink, $pdRating, $pdTags, $pdVideo);
				$output		= str_replace($fileLayoutParams['search'], $replace, $fileLayout);
				
				echo $output;
			}

			// ---------------------------------------------------	
			
			
			$o = '<div class="pd-cb">&nbsp;</div>';
			
			if ((int)$v->confirm_license > 0) {
				$o .= '<h4 class="pdfv-confirm-lic-text">'.JText::_('COM_PHOCADOWNLOAD_LICENSE_AGREEMENT').'</h4>';
				$o .= '<div id="phoca-dl-license" style="height:'.(int)$this->t['licenseboxheight'].'px">'.$v->licensetext.'</div>';
				
				// External link
				if ($v->link_external != '' && $v->directlink == 1) {	
					$o .= '<form action="" name="phocaDownloadForm" id="phocadownloadform" target="'.$this->t['download_external_link'].'">';	
					$o .= '<input type="checkbox" name="license_agree" onclick="enableDownloadPD()" /> <span>'.JText::_('COM_PHOCADOWNLOAD_I_AGREE_TO_TERMS_LISTED_ABOVE').'</span> ';
					$o .= '<input class="btn" type="button" name="submit" onClick="location.href=\''.$v->link_external.'\';" id="pdlicensesubmit" value="'.JText::_('COM_PHOCADOWNLOAD_DOWNLOAD').'" />';
				} else {
					$o .= '<form action="'.htmlspecialchars($this->t['action']).'" method="post" name="phocaDownloadForm" id="phocadownloadform">';
					$o .= '<input type="checkbox" name="license_agree" onclick="enableDownloadPD()" /> <span>'.JText::_('COM_PHOCADOWNLOAD_I_AGREE_TO_TERMS_LISTED_ABOVE').'</span> ';
					$o .= '<input class="btn" type="submit" name="submit" id="pdlicensesubmit" value="'.JText::_('COM_PHOCADOWNLOAD_DOWNLOAD').'" />';
					$o .= '<input type="hidden" name="download" value="'.$v->id.'" />';
					$o .= '<input type="hidden" name="'. JSession::getFormToken().'" value="1" />';
				}
				$o .= '</form>';

				// For users who have disabled Javascript
				$o .= '<script type=\'text/javascript\'>document.forms[\'phocadownloadform\'].elements[\'pdlicensesubmit\'].disabled=true</script>';
			} else {
				// External link
				if ($v->link_external != '') {	
					$o .= '<form action="" name="phocaDownloadForm" id="phocadownloadform" target="'.$this->t['download_external_link'].'">';
					$o .= '<input class="btn" type="button" name="submit" onClick="location.href=\''.$v->link_external.'\';" id="pdlicensesubmit" value="'.JText::_('COM_PHOCADOWNLOAD_DOWNLOAD').'" />';
				} else {
					$o .= '<form action="'.htmlspecialchars($this->t['action']).'" method="post" name="phocaDownloadForm" id="phocadownloadform">';
					$o .= '<input class="btn" type="submit" name="submit" id="pdlicensesubmit" value="'.JText::_('COM_PHOCADOWNLOAD_DOWNLOAD').'" />';
					$o .= '<input type="hidden" name="license_agree" value="1" />';
					$o .= '<input type="hidden" name="download" value="'.$v->id.'" />';
					$o .= '<input type="hidden" name="'. JSession::getFormToken().'" value="1" />';
				}
				$o .= '</form>';
			}

			
			if ($this->t['display_file_comments'] == 1) {
				if (JComponentHelper::isEnabled('com_jcomments', true)) {
					include_once(JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
					$o .= JComments::showComments($v->id, 'com_phocadownload_files', JText::_('COM_PHOCADOWNLOAD_FILE') .' '. $v->title);
				}
			}
			
			if ($this->t['display_file_comments'] == 2) {
				$o .= '<div class="pd-fbcomments">'.$this->loadTemplate('comments-fb').'</div>';
			}
			
			echo $o;
		
		} else {
			echo '<h3 class="pd-filename-txt">'.JText::_('COM_PHOCADOWNLOAD_FILE') .'</h3>';
			echo '<div class="pd-error">'.JText::_('COM_PHOCADOWNLOAD_NO_RIGHTS_ACCESS_CATEGORY').'</div>';
		}
	}
} 