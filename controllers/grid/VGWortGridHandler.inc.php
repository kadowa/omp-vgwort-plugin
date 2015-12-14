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

class VGWortGridHandler extends GridHandler {
	/** @var StaticPagesPlugin The static pages plugin */
	static $plugin;

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

		// Set the grid details.
		$this->setTitle('plugins.generic.vgWort.vgWort');
		$this->setInstructions('plugins.generic.vgWort.description');
		
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');
		$monographDao = DAORegistry::getDAO('MonographDAO');
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$publishedMonographFactory = $publishedMonographDao->getByPressId($context->getId());
		$monographFactory = $monographDao->getByPressId($context->getId());
		
		while ($monograph = $monographFactory->next()) {
			$submissionId = $monograph->getId();
			$files = $submissionFileDao->getBySubmissionId($submissionId);
			$this->setGridDataElements($files);
 			foreach ($files as $file) {
				$file->setData('vgWortPixel', "test123");
				$submissionFileDao->updateDataObjectSettings(
						'submission_file_settings',
						$file,
						array('file_id' => $file->getFileId())
				);
		
			}
		}
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
		$form = new Form(self::$plugin->getTemplatePath() . 'vgWort.tpl');
		$json = new JSONMessage(true, $form->fetch($request));
		return $json->getString();
	}
}

?>
