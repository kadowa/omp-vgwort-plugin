<?php

/**
 * @file plugins/metadata/vgWort/form/VGWort.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortForm
 * @ingroup plugins_generic_vgwort
 *
 * @brief Form for press managers to setup DOI plugin
 */

import('lib.pkp.classes.form.Form');

class VGWortSettingsForm extends Form {

	//
	// Private properties
	//
	/** @var integer */
	var $_pressId;
	
	/** @var VGWortPlugin */
	var $_plugin;

	/**
	 * Get the plugin.
	 * @return Xmdp22MetadataPlugin
	 */
	function &_getPlugin() {
		return $this->_plugin;
	}


	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin VGWortPlugin
	 * @param $pressId integer
	 */
	function VGWortSettingsForm(&$plugin, $pressId) {
		$this->_pressId = $pressId;
		$this->_plugin =& $plugin;
		
		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');

		$this->addCheck(new FormValidatorUrl($this, 'domain', 'optional', ''));
		$this->addCheck(new FormValidatorUrl($this, 'domain_alternative', 'optional', ''));

		$this->addCheck(new FormValidatorPost($this));

		$this->setData('pluginName', $plugin->getName());
	}


	//
	// Implement template methods from Form
	//
	/**
	 * @see Form::initData()
	 */
	function initData() {
		$pressId = $this->_getPressId();
		$plugin =& $this->_getPlugin();
		
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($pressId, $fieldName));
		}
	}

	/**
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}

	/**
	 * @see Form::execute()
	 */
	function execute() {
		$plugin =& $this->_getPlugin();
		$pressId = $this->_getPressId();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($pressId, $fieldName, $this->getData($fieldName), $fieldType);
		}
	}
	
	//
	// Private helper methods
	//
	function _getFormFields() {
		return array(
			'domain' => 'string',
			'domain_alternative' => 'string',
		);
	}
	
	/**
	 * Get the press ID.
	 * @return integer
	 */
	function _getPressId() {
		return $this->_pressId;
	}
}

?>
