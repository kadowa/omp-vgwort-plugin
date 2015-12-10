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

	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			//HookRegistry::call('SubmissionHandler::saveSubmit', array($step, &$submission, &$submitForm))
			HookRegistry::register('Templates::Management::Settings::website', array($this, 'callbackShowWebsiteSettingsTabs'));
			// Register the components this plugin implements to
			// permit administration of static pages.
			HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
		}
		return $success;
	}
	
	/**
	 * Extend the website settings tabs to include static pages
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackShowWebsiteSettingsTabs($hookName, $args) {
		$output =& $args[2];
		$request =& Registry::get('request');
		$dispatcher = $request->getDispatcher();
	
		// Add a new tab for static pages
		$output .= '<li><a name="vgWort" href="' . $dispatcher->url($request, ROUTE_COMPONENT, null, 'plugins.generic.vgWort.controllers.grid.VGWortGridHandler', 'index') . '">' . __('plugins.generic.vgWort.vgWort') . '</a></li>';
	
		// Permit other plugins to continue interacting with this hook
		return false;
	}
	
	/**
	 * Permit requests to the static pages grid handler
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
		}
		return false;
	}
	
	function addVGWCounter($submissionFile, $pixel) {
		$submissionFile->setData('vgwortpixel', $pixel);
	}

	function getDisplayName() {
		return __('plugins.generic.vgWort.displayName');
	}

	function getDescription() {
		return __('plugins.generic.vgWort.description');
	}
	
	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
	
	
}
?>