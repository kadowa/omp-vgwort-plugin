<?php

/**
 * @file controllers/form/VGWortPixelForm.inc.php
*
* Copyright (c) 2014-2015 Simon Fraser University Library
* Copyright (c) 2003-2015 John Willinsky
* Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*
* @class VGWortPixelForm
* @ingroup controllers_form_VGWortPixelForm
*
* Form to add VG Wort pixels
*
*/

import('lib.pkp.classes.form.Form');

class VGWortFileForm extends Form {
	var $_submissionId;

	var $_submissionFileId;

	var $_formParams;
	
	var $vgWortPublic;
	
	var $vgWortPrivate;

	/**
	 * Constructor
	 * @param $plugin The VG Wort plugin
	 * @param $submissionId int Submission ID
	 * @param $submissionFileId str Submission File ID
	 */
	function VGWortFileForm($plugin, $submissionId, $submissionFileId, $formParams = null) {
		parent::Form($plugin->getTemplatePath() . 'editSubmissionFileCounters.tpl');

		$this->_submissionId = $submissionId;
		$this->_submissionFileId = $submissionFileId;
		$this->_formParams = $formParams;
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('vgWortPublic', 'vgWortPrivate'));
	}
	
	/**
	 * Initialize form data from the database.
	 */
	function initData() {
		if ($this->_submissionFileId) {
			$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
			$file = $submissionFileDao->getLatestRevision($this->_submissionFileId);
			
			$this->_data = array(
					'vgWortPublic' => $file->getData('vgWortPublic'),
					'vgWortPrivate' => $file->getData('vgWortPrivate'),
			);
		}
	}

	/**
	 * @see Form::fetch
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager();
		
		$templateMgr->assign('submissionId', $this->_submissionId);
		$templateMgr->assign('submissionFileId', $this->_submissionFileId);
		
		$templateMgr->assign('vgWortPublic', $this->getData('vgWortPublic'));
		$templateMgr->assign('vgWortPrivate', $this->getData('vgWortPrivate'));
		
		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		parent::execute();
 		$public = $this->getData('vgWortPublic');
 		$private = $this->getData('vgWortPrivate');
 		$submissionFileId = $this->_submissionFileId;

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$file = $submissionFileDao->getLatestRevision($submissionFileId);

		$file->setData('vgWortPublic', $public);
		$file->setData('vgWortPrivate', $private);
		
		$submissionFileDao->updateDataObjectSettings(
				'submission_file_settings',
				$file,
				array('file_id' => $file->getFileId())
		);
		
		return $submissionFileId;
	}
}

?>
