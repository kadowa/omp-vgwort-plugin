{**
 * templates/editStaticPageForm.tpl
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form for editing a static page
 *}

 <script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#vgWortPixelForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
 
{url|assign:actionUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.VGWortGridHandler" op="updateSubmissionFile" submissionId=$submissionId escape=false}
 <form class="pkp_form" id="vgWortPixelForm" method="post" action="{$actionUrl}">
 	<input type="hidden" name="submissionId" value="{$submissionId|escape}" />
	<input type="hidden" name="submissionFileId" value="{$submissionFileId|escape}" />

	{fbvFormArea id="pixelFields"}
		{fbvFormSection title="plugins.generic.vgWort.submissionMetadataFormPublic" required=false}
			<p class="pkp_help">{translate key="plugins.generic.vgWort.submissionMetadataFormPublicGlobal"}</p>
			{fbvElement type="text" id="vgWortPublic" value=$vgWortPublic multilingual=false maxlength="255"}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons id="vgwortPixelFormSubmit" submitText="common.save"}
</form>
