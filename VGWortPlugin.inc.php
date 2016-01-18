<?php

/**
 * @file plugins/generic/vgWort/VGWortPlugin.inc.php
 *
 * Copyright (c) 2013-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortPlugin
 * @ingroup plugins_generic_vgwort
 *
 * @brief VG Wort plugin class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class VGWortPlugin extends GenericPlugin {
	function getDisplayName() {
		return __('plugins.generic.vgWort.displayName');
	}
	
	function getDescription() {
		return __('plugins.generic.vgWort.description');
	}
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			// Register VG Wort tab in catalog
			HookRegistry::register('Templates::Controllers::Modals::SubmissionMetadata::CatalogEntryTabs::Tabs', array($this, 'showVGWortTab'));
	
			// Register the components this plugin implements.
			HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
				
			// Register addition of VG Wort pixels to submission file settings table
			HookRegistry::register('submissionfiledao::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			HookRegistry::register('monographfiledao::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			HookRegistry::register('submissionfiledaodelegate::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			HookRegistry::register('monographfiledaodelegate::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			
			// Hook for adding pixel tags to templates
			HookRegistry::register ('TemplateManager::display', array($this, 'handleTemplateDisplay'));
		}
		return $success;
	}
	
	/**
	 * @see PKPPlugin::getManagementVerbs()
	 */
	function getManagementVerbs() {
		return array(array('settings', __('manager.plugins.settings')));
	}
	
	function getActions($request, $actionArgs) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
				$this->getEnabled()?array(
						new LinkAction(
								'settings',
								new AjaxModal(
										$router->url($request, null, null, 'manage', null, $actionArgs),
										$this->getDisplayName()
								),
								__('manager.plugins.settings'),
								null
						),
				):array(),
				parent::getActions($request, $actionArgs)
		);
	}
	
	/**
	 * @copydoc PKPPlugin::manage()
	 */
	function manage($args, $request) {
		$notificationManager = new NotificationManager();
		$user = $request->getUser();
		$press = $request->getPress();
	
		$settingsFormName = $this->getSettingsFormName();
		$settingsFormNameParts = explode('.', $settingsFormName);
		$settingsFormClassName = array_pop($settingsFormNameParts);
		$this->import($settingsFormName);
		$form = new $settingsFormClassName($this, $press->getId());
		
		if ($request->getUserVar('save')) {
			$form->readInputData();
			if ($form->validate()) {
				$form->execute();
				$notificationManager->createTrivialNotification($user->getId(), NOTIFICATION_TYPE_SUCCESS);
				return new JSONMessage(true);
			} else {
				return new JSONMessage(true, $form->fetch($request));
			}
		} else {
			$form->initData();
			return new JSONMessage(true, $form->fetch($request));
		}
	}

	
	/**
	 * @see PubIdPlugin::getSettingsFormName()
	 */
	function getSettingsFormName() {
		return 'form.VGWortSettingsForm';
	}

	/**
	 * Extend the website settings tabs to include VG Wort
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function showVGWortTab($hookName, $args) {
		if ( !$this->getEnabled() ) {
			return;
		}
		
		$smarty =& $args[1];
		$output =& $args[2];
		$request =& Registry::get('request');
		$dispatcher = $request->getDispatcher();

		$submissionId = $smarty->get_template_vars('submissionId');
		$stageId = $smarty->get_template_vars('stageId');
		$counter = $smarty->get_template_vars('counter');

		// Add a new catalog entry tab
		$output .= '<li><a name="vgWort" href='
			. $dispatcher->url($request, ROUTE_COMPONENT, null,
					"plugins.generic.vgWort.controllers.modal.VGWortCatalogEntryTabHandler",
					'vgWortMetadata', null,
					array('stageId' => $stageId, 'submissionId' => $submissionId, 'tabPos' => $counter, 'tab' => 'vgwort'))
			. '>' . __('plugins.generic.vgWort.vgWort') . '</a></li>';

		// It's not nice to touch this, but I don't see any other option
		$smarty->_tpl_vars['counter'] = $counter+1;
	}
	
	/*
	 * Add field for VG Wort pixels in the submission_file_settings table
	 */
	function addVGWortPixelMetadataField($hookName, $params) {
		$returner =& $params[1];
		$returner[] = "vgWortPublic";
		$returner[] = "vgWortPrivate";

		return false;
	}
	
	/*
	 * Look for book template
	 */
	function handleTemplateDisplay($hookName, $args) {
		$templateManager =& $args[0];
		$template =& $args[1];
	
		switch ( $template ) {
			case 'frontend/pages/book.tpl':
				$templateManager->register_outputfilter(array($this, 'addPixelToLink'));
		}
	}
	
	/*
	 * Add pixel to PDF download/view links
	 */
	function addPixelToLink($output, &$smarty) {
		$smarty->unregister_outputfilter('addPixelToLink');
	
		$output = preg_replace_callback (
				'|http:\/\/.*catalog\/view(\/[0-9-]+)+|',
				array($this, "addCounterToURL"),
				$output
				);
			
		$output = preg_replace_callback (
				'|http:\/\/.*catalog\/download(\/[0-9-]+)+|',
				array($this, "addCounterToURL"),
				$output
				);

		return $output;
	}
	
	function addCounterToURL($match) {
		$matches = [];
		preg_match('|[0-9]+-[0-9]+|', $match[0], $matches);
		$submissionFileId = $matches[0];
		
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$file = $submissionFileDao->getLatestRevision($submissionFileId);
		
		$publicCode = $file->getData('vgWortPublic');
		
		if ( $publicCode ) {
					return "http://" . $this->getDomain() . "/na/" . $publicCode . "?l=" . $match[0];	
		}
		return $match[0];
	}
	
	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
		
	}
	
	function getDomain() {
		$domain = $this->getSetting(null, "domain");
		
		if ( !$domain ) {
			$domain = "vg01.met.vgwort.de"; //default
		}
		
		return $domain;
	}
	
	/**
	 * Permit requests to the VG Wort grid handler
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.vgWort.controllers.grid.VGWortGridHandler') {
			// Allow the grid handler to get the plugin object
			import($component);
			VGWortGridHandler::setPlugin($this);
			return true;
		} else if ($component == 'plugins.generic.vgWort.controllers.modal.VGWortCatalogEntryTabHandler') {
			// Allow the grid handler to get the plugin object
			import($component);
			VGWortCatalogEntryTabHandler::setPlugin($this);
			return true;
		}
		return false;
	}
}

?>
