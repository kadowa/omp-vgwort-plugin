<?php

/**
 * @file plugins/generic/vgWort/controllers/modal/VGWortCatalogEntryTabHandler.inc.php
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

class VGWortCatalogEntryTabHandler extends Handler {
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
		parent::Handler();
		//TODO: Handle switching between tabs, see CatalogEntryTabHandler or PublicationEntryTabHandler
	}

	function index($args, $request) {
		import('plugins.generic.vgWort.form.VGWortPixelForm');
		
		$form = new VGWortPixelForm($this::$plugin, $contextId=1, $submissionId = 18);
		return new JSONMessage(true, $form->fetch($request));
	}


}

?>
