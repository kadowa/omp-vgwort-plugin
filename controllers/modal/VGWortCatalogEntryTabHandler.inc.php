<?php

/**
 * @file plugins/generic/vgWort/controllers/modal/eVGWortCatalogEntryTabHandler.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortCatalogEntryTabHandler
 *
 * @brief Handle AJAX operations for VG Wort catalog tab.
 */

// Import the base Handler.
import('classes.handler.Handler');
import('lib.pkp.controllers.tab.publicationEntry.PublicationEntryTabHandler');
import('controllers.tab.catalogEntry.CatalogEntryTabHandler');

class VGWortCatalogEntryTabHandler extends CatalogEntryTabHandler {
	/** @var VGWortPlugin The VG Wort plugin */
	static $plugin;
	
	/**
	 * Set the VG Wort plugin.
	 * @param $plugin VGWortPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}
	
	/**
	 * Constructor
	 */
	function VGWortCatalogEntryTabHandler() {
		parent::CatalogEntryTabHandler();

		$this->addRoleAssignment(
				array(ROLE_ID_SUB_EDITOR, ROLE_ID_MANAGER),
				array(
						'vgWortMetadata',
						'saveForm',
				)
		);
	}

	/**
	 * Show the catalog metadata form.
	 * @param $request Request
	 * @param $args array
	 * @return JSONMessage JSON object
	 */
	function vgWortMetadata($args, $request) {
		import('plugins.generic.vgWort.form.VGWortForm');

		$submission = $this->getSubmission();
		$stageId = $this->getStageId();
		$context = $request->getContext();

		$form = new VGWortForm($this::$plugin, $context->getId(), $submission->getId(), $stageId, array('displayedInContainer' => True, 'tabPos' => $this->getTabPosition()));
		//$form->initData($args, $request);
		return new JSONMessage(true, $form->fetch($request));

	}

	function _getFormFromCurrentTab(&$form, &$notificationKey, $request) {
		parent::_getFormFromCurrentTab($form, $notificationKey, $request); // give PKP-lib a chance to set the form and key.

		switch ($this->getCurrentTab()) {
			case 'vgwort':
				$form = $this->_getVGWortForm($request);
				$notificationKey = 'plugins.generic.vgWort.notification.savedForm';
				break;
		}
	}

	function _getVGWortForm($request) {
		$submission = $this->getSubmission();
		$stageId = $this->getStageId();
		$context = $request->getContext();

		import('plugins.generic.vgWort.form.VGWortForm');
		return new VGWortForm($this::$plugin, $contextId=$context->getId(), $submissionId = $submission->getId(), $stageId, array('displayedInContainer' => True, 'tabPos' => $this->getTabPosition()));
	}

	function saveForm($args, $request) {
		$json = new JSONMessage();
		$form = null;

		error_log($this->getCurrentTab());

		$submission = $this->getSubmission();
		$stageId = $this->getStageId();
		$notificationKey = null;

		$this->_getFormFromCurrentTab($form, $notificationKey, $request);

		if ($form) { // null if we didn't have a valid tab
			$form->readInputData();
			if($form->validate($request)) {
				$form->execute($request);
				// Create trivial notification in place on the form
				$notificationManager = new NotificationManager();
				$user = $request->getUser();
				$notificationManager->createTrivialNotification($user->getId(), NOTIFICATION_TYPE_SUCCESS, array('contents' => __($notificationKey)));
			} else {
				// Could not validate; redisplay the form.
				$json->setStatus(true);
				$json->setContent($form->fetch($request));
			}

			if ($request->getUserVar('displayedInContainer')) {
				$router = $request->getRouter();
				$dispatcher = $router->getDispatcher();
				$url = $dispatcher->url($request, ROUTE_COMPONENT, null, $this->_getHandlerClassPath(), 'fetch', null, array('submissionId' => $submission->getId(), 'stageId' => $stageId, 'tabPos' => $this->getTabPosition(), 'hideHelp' => true));
				$json->setAdditionalAttributes(array('reloadContainer' => true, 'tabsUrl' => $url));
				$json->setContent(true); // prevents modal closure
			}
			return $json;
		} else {
			fatalError('Unknown or unassigned format id!');
		}
	}
}

?>
