<?php

/**
 * @file controllers/grid/VGWortGridHandler.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortGridGridHandler
 * @ingroup controllers_grid_vgWort
 *
 * @brief Handle VG Wort grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.vgWort.controllers.grid.VGWortGridRow');
import('plugins.generic.vgWort.controllers.grid.VGWortGridCellProvider');

class VGWortGridHandler extends GridHandler {
	/** @var StaticPagesPlugin The static pages plugin */
	static $plugin;

	var $submissionId;
	
	/**
	 * Set the static pages plugin.
	 * @param $plugin StaticPagesPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Constructor
	 */
	function VGWortGridHandler() {
		parent::GridHandler();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('index', 'fetchGrid')
		);
	}


	//
	// Overridden template methods
	//
	/**
	 * @copydoc Gridhandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request);
		$context = $request->getContext();
		
		$this->submissionId = $request->getUserVar('submissionId');
		
		error_log($this->submissionId);

		// Set the grid details.
		$this->setTitle('plugins.generic.vgWort.vgWortGrid');
		$this->setInstructions('plugins.generic.vgWort.description');
		$this->setEmptyRowText('plugins.generic.vgWort.emptyRow');

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		
		$files = $submissionFileDao->getLatestRevisions($this->submissionId, 10);//getBySubmissionId($this->submissionId);

		$this->setGridDataElements($files);

		// Columns
		$cellProvider = new VGWortGridCellProvider();
		$this->addColumn(new GridColumn(
				'name',
				'plugins.generic.vgWort.submissionFiles',
				null,
				'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
				$cellProvider
		));
		$this->addColumn(new GridColumn(
				'publicIdentifier',
				'plugins.generic.vgWort.submissionMetadataFormPublic',
				null,
				'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
				$cellProvider
		));
		$this->addColumn(new GridColumn(
				'privateIdentifier',
				'plugins.generic.vgWort.submissionMetadataFormPrivate',
				null,
				'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
				$cellProvider
		));
	}
	
	//
	// Overridden methods from GridHandler
	//
	/**
	* @copydoc Gridhandler::getRowInstance()
	*/
	function getRowInstance() {
		return new VGWortGridRow();
	}


	//
	// Public Grid Actions
	//
	/**
	 * Display the grid's containing page.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function index($args, $request) {
		$context = $request->getContext();
		import('lib.pkp.classes.form.Form');

		$form = new Form(self::$plugin->getTemplatePath() . 'vgWortMetadata.tpl');
		$json = new JSONMessage(true, $form->fetch($request));

		return $json->getString();
	}
	
	function editSubmissionFile($args, $request) {
		$submissionFileId = $request->getUserVar('submissionFileId');
		
		error_log($submissionFileId);
		
  		$context = $request->getContext();
		$this->setupTemplate($request);
		
		// Create and present the edit form

		import('plugins.generic.vgWort.form.VGWortPixelForm');

		$form = new VGWortPixelForm(self::$plugin, $context->getId(), $submissionFileId);
	
		$json = new JSONMessage(true, $form->fetch($request));
		return $json->getString();
	}
	
	function updateSubmissionFile($args, $request) {
		$submissionFileId = $request->getUserVar('submissionFileId');
		$context = $request->getContext();
		$this->setupTemplate($request);
		
		// Create and populate the form
		import('plugins.generic.vgWort.form.VGWortPixelForm');

		$form = new VGWortPixelForm(self::$plugin, $context->getId(), $submissionFileId);
		$form->readInputData();
		
		// Check the results
		if ($form->validate()) {
			// Save the results
			$form->execute();
			return DAO::getDataChangedEvent(); //TODO: Display nicely (look at js?)
		} else {
			// Present any errors
			$json = new JSONMessage(true, $form->fetch($request));
			return $json->getString();
		}
	}
}

?>


