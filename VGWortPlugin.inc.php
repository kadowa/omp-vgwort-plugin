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
			
			// register addition of VG Wort pixels to submission file settings table
			HookRegistry::register('submissionfiledao::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			HookRegistry::register('monographfiledao::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			HookRegistry::register('submissionfiledaodelegate::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
			HookRegistry::register('monographfiledaodelegate::getAdditionalFieldNames', array($this, 'addVGWortPixelMetadataField'));
		}
		return $success;
	}
	

	/**
	 * Extend the website settings tabs to include VG Wort
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function showVGWortTab($hookName, $args) {
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
	 * Insert VG Wort Pixel into the submission_file_settings table
	 */
	function addVGWortPixel($submissionFile, $public) {
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		
		$submissionFile->setData('vgWortPublic', $public);
		$submissionFileDao->updateDataObjectSettings(
			'submission_file_settings',
			$submissionFile,
			array('file_id' => $submissionFile->getFileId())
		);
	}
	
	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
	
	// Not in use
	
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
