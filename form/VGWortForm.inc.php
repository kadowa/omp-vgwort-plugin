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
	var $_contextId;
	
	var $_submissionId;
	
	var $_stageId;
	
	var $_formParams;
	
	var $vgWortPublic;
	
	var $vgWortPrivate;
	
	/**
	 * Constructor
	 * @param $staticPagesPlugin StaticPagesPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function VGWortForm($plugin, $contextId, $submissionId, $stageId, $formParams = null) {
		parent::Form($plugin->getTemplatePath() . 'vgWortCatalogTab.tpl');
	
		$this->_contextId = $contextId;
		$this->_submissionId = $submissionId;
		$this->_stageId = $stageId;
		$this->_formParams = $formParams;
	}

	/**
	 * Initialize form data from the database.
	 */
	function initData() {
		if ($this->_submissionId) {
			$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
			$files = $submissionFileDao->getLatestRevisions($this->_submissionId, 10);

			$global = True;
			$public;
			// check, wether all submission files have the same public code (global assignment)
			foreach ($files as $file) {
				if ( isset($public) ) {
					if ( $public != $file->getData('vgWortPublic') ) {
						$global = False; // public code differs => individual assignment
					}
				}
				else {
					$public = $file->getData('vgWortPublic'); //initialize
				}
			}
				
			if ( $global ) {
				$this->_data = array(
					'vgWortPublic' => $file->getData('vgWortPublic'),
					'vgWortPrivate' => $file->getData('vgWortPrivate'),
				);
			}
		}
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
		$templateMgr->assign('submissionId', $this->_submissionId);
		$templateMgr->assign('contextId', $this->_contextId);
		$templateMgr->assign('stageId', $this->_stageId);
		$templateMgr->assign('formParams', $this->_formParams);

		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		parent::execute();
		$public = $this->getData('vgWortPublic');
		$private = $this->getData('vgWortPrivate');
		$submissionId = $this->_submissionId;
		
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$files = $submissionFileDao->getBySubmissionId($submissionId);
		
		foreach ($files as $file) {
			$file->setData('vgWortPublic', $public);
			$file->setData('vgWortPrivate', $private);
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
		return $this->_formParams;
	}
}

?>
