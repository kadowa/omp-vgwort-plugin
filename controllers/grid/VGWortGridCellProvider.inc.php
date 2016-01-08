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
import('lib.pkp.classes.linkAction.request.RedirectAction');

class VGWortGridCellProvider extends GridCellProvider {
	/**
	 * Constructor
	 */
	function VGWortGridCellProvider() {
		parent::GridCellProvider();
	}


	//
	// Template methods from GridCellProvider
	//
	/**
	 * Get cell actions associated with this row/column combination
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array an array of LinkAction instances
	 */
	function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_DEFAULT) {
		$submissionFile = $row->getData();

		switch ($column->getId()) {
			case 'name':
				$dispatcher = $request->getDispatcher();
				return array(new LinkAction(
					'details',
					new RedirectAction(
						$dispatcher->url($request, ROUTE_PAGE, null) . '/' . $vgWortPublic->getPath(),
						'vgWortPublic'
					),
					$vgWortPublic->getPath()
				));
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
		$vgWortPublic = $row->getData();

		switch ($column->getId()) {
			case 'path':
				// The action has the label
				return array('label' => '');
			case 'title':
				return array('label' => $vgWortPublic->getLocalizedTitle());
		}
	}
}

?>
