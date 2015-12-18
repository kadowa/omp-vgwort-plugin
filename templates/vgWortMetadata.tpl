{**
 * templates/vgWortMetadata.tpl
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * VG Wort plugin -- displays the VGWort pixel form fields.
 *}

 <form class="pkp_form" id="vgWortMetadataForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.modal.VGWortCatalogEntryTabHandler" op="saveForm"}">
{fbvFormArea id="pixelFields"}
	{fbvFormSection title="plugins.generic.vgWort.submissionMetadataFormPublic" required=false}
		{fbvElement type="text" id="vgWortPublic" value="" multilingual=false maxlength="255"}
	{/fbvFormSection}
	{fbvFormSection title="plugins.generic.vgWort.submissionMetadataFormPrivate" required=false}
		{fbvElement type="text" id="vgWortPrivate" value="" multilingual=false maxlength="255"}
	{/fbvFormSection}
{/fbvFormArea}
</form>