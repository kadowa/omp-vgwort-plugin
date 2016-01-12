<?php

/**
 * @file controllers/form/VGWortForm.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortForm
 * @ingroup controllers_form_VGWortForm
 *
 * Form to add VG Wort pixels
 *
 */

import('lib.pkp.classes.form.Form');

class VGWortForm extends Form {
	var $contextId;
	
	var $submissionId;
	
	var $stageId;
	
	var $formParams;
	
	var $vgWortPublic;
	
	/**
	 * Constructor
	 * @param $staticPagesPlugin StaticPagesPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function VGWortForm($plugin, $contextId, $submissionId, $stageId, $formParams = null) {
		parent::Form($plugin->getTemplatePath() . 'vgWortMetadata.tpl');
	
		$this->contextId = $contextId;
		$this->submissionId = $submissionId;
		$this->stageId = $stageId;
		$this->formParams = $formParams;
	}

	/**
	 * Initialize form data from current group.
	 */
	function initData() {
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('vgWortPublic', 'vgWortPrivate'));
	}

	/**
	 * @see Form::fetch
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign('submissionId', $this->submissionId);
		$templateMgr->assign('contextId', $this->contextId);
		$templateMgr->assign('stageId', $this->stageId);
		$templateMgr->assign('formParams', $this->formParams);

		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		parent::execute();
		$public = $this->getData('vgWortPublic');
		$submissionId = $this->submissionId;
		
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$files = $submissionFileDao->getBySubmissionId($submissionId);
		
		foreach ($files as $file) {
			$file->setData('vgWortPublic', $public);
			$submissionFileDao->updateDataObjectSettings(
					'submission_file_settings',
					$file,
					array('file_id' => $file->getFileId())
			);
		}
	}
	
	/**
	 * Get the extra form parameters.
	 */
	function getFormParams() {
		return $this->formParams;
	}
}

?>
