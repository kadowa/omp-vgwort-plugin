<?php

/**
 * @file controllers/grid/form/StaticPageForm.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageForm
 * @ingroup controllers_grid_staticPages
 *
 * Form for press managers to create and modify sidebar blocks
 *
 */

import('lib.pkp.classes.form.Form');

class VGWortPixelForm extends Form {
	/**
	 * Constructor
	 * @param $staticPagesPlugin StaticPagesPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function VGWortPixelForm($plugin, $contextId, $submissionId = null) {
		parent::Form($plugin->getTemplatePath() . 'vgWortMetadata.tpl');
		
		$this->contextId = $contextId;
		$this->submissionId = $submissionId;

		// Add form checks
		//$this->addCheck(new FormValidatorPost($this));
	}

	/**
	 * Initialize form data from current group group.
	 */
	function initData() {
		//TODO
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

		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		//TODO
	}
}

?>
