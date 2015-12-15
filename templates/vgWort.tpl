{**
 * templates/vgWort.tpl
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * VG Wort plugin -- displays the VGWortGrid.
 * {url|assign:vgWortGridUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.VGWortGridHandler" op="fetchGrid" escape=false}
 * {load_url_in_div id="vgWortGridContainer" url=$vgWortGridUrl}
 *}

{fbvFormArea id="additionalMetaData"}
	{fbvFormSection title="plugins.generic.vgWort.submissionMetadataForm" required=false}
		{fbvElement type="text" id="vgwort" value=$submissionFile->getData('vgWortPixel', '') multilingual=false maxlength="255"}
	{/fbvFormSection}
{/fbvFormArea}
