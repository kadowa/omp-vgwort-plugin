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

		}
		return $success;
	}

	function getDisplayName() {
		return __('plugins.generic.vgWort.displayName');
	}

	function getDescription() {
		return __('plugins.generic.vgWort.description');
	}
	
}
?>