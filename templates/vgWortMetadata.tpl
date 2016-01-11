{**
 * templates/vgWortMetadata.tpl
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * VG Wort plugin -- displays the VGWort pixel form fields.
 *}
 
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#vgWortMetadataForm').pkpHandler(
			'$.pkp.controllers.catalog.form.CatalogMetadataFormHandler',
			{ldelim}
				trackFormChanges: true,
				$uploader: $('#plupload_catalogMetadata'),
				uploaderOptions: {ldelim}
					uploadUrl: {url|json_encode router=$smarty.const.ROUTE_COMPONENT component="tab.catalogEntry.CatalogEntryTabHandler" op="uploadCoverImage" escape=false stageId=$stageId submissionId=$submissionId},
					baseUrl: {$baseUrl|json_encode}
				{rdelim},
				arePermissionsAttached: {if $arePermissionsAttached}true{else}false{/if}
			{rdelim}
		);
	{rdelim});
</script>

<form class="pkp_form" id="vgWortMetadataForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.modal.VGWortCatalogEntryTabHandler" op="saveForm"}">
	<input type="hidden" name="submissionId" value="{$submissionId|escape}" />
	<input type="hidden" name="stageId" value="{$stageId|escape}" />
	<input type="hidden" name="tabPos" value="{$tabPos}" />
	<input type="hidden" name="tab" value="vgwort" />
	<input type="hidden" name="displayedInContainer" value="{$formParams.displayedInContainer|escape}" />

	{fbvFormArea id="pixelFields"}
		{fbvFormSection title="plugins.generic.vgWort.submissionMetadataFormPublic" required=false}
			<p class="pkp_help">{translate key="plugins.generic.vgWort.submissionMetadataFormPublicGlobal"}</p>
			{fbvElement type="text" id="vgWortPublic" value=$vgWortPublic multilingual=false maxlength="255"}
		{/fbvFormSection}
	{/fbvFormArea}

	
	{url|assign:representationsGridUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.VGWortGridHandler" op="fetchGrid" submissionId=$submissionId escape=false}
	{load_url_in_div id="formatsGridContainer"|uniqid url=$representationsGridUrl}

	{fbvFormButtons id="vgwortMetadataFormSubmit" submitText="common.save"}
</form>
