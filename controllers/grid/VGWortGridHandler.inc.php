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

	var $_submissionId;
	
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
			array('index', 'fetchGrid', 'getRowInstance', 'editSubmissionFile', 'updateSubmissionFile')
		);
	}
	
	function setSubmissionId($submissionId) {
		$this->_submissionId = $submissionId;
	}

	function getSubmissionId() {
		return $this->_submissionId;
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
		
		$this->setSubmissionId($request->getUserVar('submissionId'));
		// Set the grid details.
		$this->setTitle('plugins.generic.vgWort.vgWortGrid');
		$this->setInstructions('plugins.generic.vgWort.description');
		$this->setEmptyRowText('plugins.generic.vgWort.emptyRow');

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$files = $submissionFileDao->getLatestRevisions($this->getSubmissionId(), 10);//getBySubmissionId($this->submissionId);
		
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
		return new VGWortGridRow($this->getSubmissionId());
	}
	
	function getRequestArgs() {
		$submissionId = $this->getSubmissionId();

		return array(
				'submissionId' => $submissionId = $this->getSubmissionId(),
		);
	}

	/**
	 * @see GridHandler::loadData
	 */
	function loadData($request, $filter = null) {
		$submissionId = $this->getSubmissionId();
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$data = $submissionFileDao->getLatestRevisions($request->getUserVar('submissionId'), 10);//getBySubmissionId($this->submissionId);
		
		error_log("Get?: " . $request->isGet());
		error_log("URL: " . $request->getCompleteUrl());
		error_log("SubmissionId: " . $submissionId . ", " . $request->getUserVar('submissionId') . ", " . sizeof($data));
	
		return $data->toArray();
	}
	
	function editSubmissionFile($args, $request) {
		$submissionFileId = $request->getUserVar('submissionFileId');
		
		error_log("Submission id: " . $this->getSubmissionId());
		
  		$context = $request->getContext();
		$this->setupTemplate($request);
		
		// Create and present the edit form

		import('plugins.generic.vgWort.form.VGWortPixelForm');

		$form = new VGWortPixelForm(self::$plugin, $context->getId(), $this->getSubmissionId(), $submissionFileId);
		$form->initData();
		
		$json = new JSONMessage(true, $form->fetch($request));
		return $json->getString();
	}
	
	function updateSubmissionFile($args, $request) {
		$submissionFileId = $request->getUserVar('submissionFileId');
		$submissionId = $this->getSubmissionId();
		
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$file = $submissionFileDao->getLatestRevision($submissionFileId);		
		$vgWortPublic = $file->getData('vgWortPublic');

		$context = $request->getContext();
		$this->setupTemplate($request);
		
		// Create and populate the form
		import('plugins.generic.vgWort.form.VGWortPixelForm');

		$form = new VGWortPixelForm(self::$plugin, $context->getId(), $this->getSubmissionId(), $submissionFileId);
		$form->initData();
		$form->readInputData();

		// Check the results
		if ($form->validate()) {
			$submissionFileId = $form->execute();
			// Save the results
			if(!isset($vgWortPublic)) {
				// This is a new code
				$vgWortPublic = $file->getData('vgWortPublic', $public);
				// New added code action notification content.
				$notificationContent = __('plugins.generic.vgWort.notification.added');
			} else {
				// code edit action notification content.
				$notificationContent = __('plugins.generic.vgWort.notification.edited');
			}
			
			// Create trivial notification.
			$currentUser = $request->getUser();
			$notificationMgr = new NotificationManager();
			$notificationMgr->createTrivialNotification($currentUser->getId(), NOTIFICATION_TYPE_SUCCESS, array('contents' => $notificationContent));
			
			// Prepare the grid row data
			$row = $this->getRowInstance();
			$row->setGridId($this->getId());
			$row->setId($submissionFileId);
			$row->setData($file);
			$row->initialize($request);
			
			return DAO::getDataChangedEvent();
		} else {
			// Present any errors
			$json = new JSONMessage(true, $form->fetch($request));
			return $json->getString();
		}
	}
}

?>


