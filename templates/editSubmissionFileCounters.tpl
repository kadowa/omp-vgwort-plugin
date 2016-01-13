{**
 * templates/editSubmissionFileCounters.tpl
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
		$('#vgWortSubmissionFileForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
 
{url|assign:actionUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.VGWortGridHandler" op="updateSubmissionFile" submissionId=$submissionId escape=false}
 <form class="pkp_form" id="vgWortSubmissionFileForm" method="post" action="{$actionUrl}">
 	<input type="hidden" name="submissionId" value="{$submissionId|escape}" />
	<input type="hidden" name="submissionFileId" value="{$submissionFileId|escape}" />

	{fbvFormArea id="pixelFields" title="plugins.generic.vgWort.submissionMetadataFormIndividual"}
		<p class="pkp_help">{translate key="plugins.generic.vgWort.submissionMetadataFormIndividualHelp"}</p>
		{fbvFormSection required=false}
			{fbvElement type="text" id="vgWortPublic" inline="true" size=$fbvStyles.size.MEDIUM value=$vgWortPublic multilingual=false maxlength="255" label="plugins.generic.vgWort.submissionMetadataFormPublic"}
			{fbvElement type="text" id="vgWortPrivate" inline="true" size=$fbvStyles.size.MEDIUM value=$vgWortPrivate multilingual=false maxlength="255" label="plugins.generic.vgWort.submissionMetadataFormPrivate"}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons id="vgWortSubmissionFileFormSubmit" submitText="common.save"}
</form>
