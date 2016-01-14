{**
 * plugins/metadata/vgWort/templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * VG Wort plugin settings
 *
 *}
<div id="description">{translate key="plugins.generic.vgwort.manager.settings.description"}</div>

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#vgWortSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="vgWortSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save="true"}">
	{include file="common/formErrors.tpl"}
	{fbvFormArea id="domainFormArea" class="border" title="plugins.generic.vgwort.manager.settings.domain"}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.vgwort.manager.settings.domain.help"}</p>
			{fbvElement type="text" label="plugins.generic.vgwort.manager.settings.domain.addinfo" required=false id="domain" value=$domain size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.generic.vgwort.manager.settings.domain.alternative.addinfo" required=false id="domain_alternative" value=$domain_alternative size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
