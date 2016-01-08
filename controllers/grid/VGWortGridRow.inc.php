<?php

/**
 * @file controllers/grid/StaticPageGridRow.inc.php
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageGridRow
 * @ingroup controllers_grid_staticPages
 *
 * @brief Handle custom blocks grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class VGWortGridRow extends GridRow {
	/**
	 * Constructor
	 */
	function VGWortGridRow() {
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

		$staticPageId = $this->getId();
		if (!empty($staticPageId)) {
			$router = $request->getRouter();

			// Create the "edit static page" action
			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editStaticPage',
					new AjaxModal(
						$router->url($request, null, null, 'editStaticPage', null, array('staticPageId' => $staticPageId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);
		}
	}
}

?>
