<?php

/**
 * @file controllers/grid/VGWortGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2000-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortGridCellProvider
 * @ingroup controllers_grid_vgWortPublics
 *
 * @brief Class for a cell provider to display information about VG Wort pixels
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.controllers.grid.GridCategoryRow');
import('lib.pkp.classes.linkAction.request.RedirectAction');

class VGWortPublicGridCellProvider extends GridCellProvider {
	var $_submissionId;
	
	/**
	 * Constructor
	 */
	function VGWortPublicGridCellProvider($submissionId) {
		$this->_submissionId = $submissionId;
		parent::GridCellProvider();
	}
	
	function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_ROW_CLICK) {
		$submissionFile = $row->getData();
		$router = $request->getRouter();
	
		switch ($column->getId()) {
			case 'name':
				$dispatcher = $request->getDispatcher();
				return array(
						new LinkAction(
								'editSubmissionFile',
								new AjaxModal(
										$router->url($request, null, null, 'editSubmissionFile', null, array('submissionFileId' => $submissionFile->getFileId(), 'submissionId' => $this->_submissionId)),
										__('grid.action.edit'),
										'modal_edit',
										true),
								''
						),
				);
			default:
				return parent::getCellActions($request, $row, $column, $position);
		}
	}
	
	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$submissionFile = $row->getData();

		$fname = $this->_shortenName($submissionFile->getLocalizedName());

		switch ($column->getId()) {
			case 'name':
				return array('label' => $fname);
			case 'code':
				return array('label' => $submissionFile->getData('vgWortPublic'));
		}
	}
	
	/*
	 * Shorten long file names for display.
	 */
	function _shortenName($fname) {
		if ( ( strpos($fname, ' ')  && strpos($fname, ' ') > 50 ) ||  ( !strpos($fname, ' ') && strlen($fname) > 30 ) ) {
			return substr($fname, 0, 50) . "(...)";
		}
		
		return $fname;
	}
}

?>
