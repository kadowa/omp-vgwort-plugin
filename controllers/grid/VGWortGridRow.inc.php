<?php

/**
 * @file controllers/grid/VGWortGridRow.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortGridRow
 * @ingroup controllers_grid_vgWort
 *
 * @brief Handle custom blocks grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridCategoryRow');
import('lib.pkp.classes.linkAction.request.AjaxModal');
import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');

class VGWortGridRow extends GridCategoryRow {
	var $_submissionId;
	
	/**
	 * Constructor
	 */
	function VGWortGridRow($submissionId) {
		$this->_submissionId = $submissionId;
		parent::GridRow();
	}
	

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);

/*		$submissionFileId = $this->getId();
		if (!empty($submissionFileId)) {
			$router = $request->getRouter();

 			$this->addAction(
				new LinkAction(
					'editSubmissionFile',
					new AjaxModal(
						$router->url($request, null, null, 'editSubmissionFile', null, array('submissionFileId' => $submissionFileId, 'submissionId' => $this->_submissionId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);
		} */
	}
}

?>
