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

class VGWortPixelForm extends Form {
	var $contextId;
	
	var $submissionId;

	var $submissionFileId;

	var $formParams;
	
	var $vgWortPublic;

	/**
	 * Constructor
	 * @param $staticPagesPlugin StaticPagesPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function VGWortPixelForm($plugin, $contextId, $submissionId, $submissionFileId, $formParams = null) {
		parent::Form($plugin->getTemplatePath() . 'editPixel.tpl');

		$this->contextId = $contextId;
		$this->submissionId = $submissionId;
		$this->submissionFileId = $submissionFileId;
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('vgWortPublic', 'vgWortPrivate'));
	}
	
	/**
	 * Initialize form data from the publication date.
	 */
	function initData() {
		if ($this->submissionFileId) {
			$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
			$file = $submissionFileDao->getLatestRevision($this->submissionFileId);
			
			$this->_data = array(
					'vgWortPublic' => $file->getData('vgWortPublic'),
			);
		}
	}

	/**
	 * @see Form::fetch
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager();
		
		$templateMgr->assign('submissionId', $this->submissionId);
		$templateMgr->assign('submissionFileId', $this->submissionFileId);
		$templateMgr->assign('contextId', $this->contextId);
		
		$templateMgr->assign('vgWortPublic', $this->getData('vgWortPublic'));
		
		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		parent::execute();
 		$public = $this->getData('vgWortPublic');
		$submissionFileId = $this->submissionFileId;

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$file = $submissionFileDao->getLatestRevision($submissionFileId);

		$file->setData('vgWortPublic', $public);
		$submissionFileDao->updateDataObjectSettings(
				'submission_file_settings',
				$file,
				array('file_id' => $file->getFileId())
		);
		
		return $submissionFileId;
	}
}

?>
