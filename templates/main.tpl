{* Load the global configuration file *}
{config_load file="_global.conf" section="setup"}
{config_load file="_global.conf" section="global"}

{* Check if we have a local configuration file *}
{if $configFile}
	{* Hint: The local configuration overrides variables assigned through the global configuration *}
	{config_load file="{$configFile}" section="local"}
{/if}

{* Include a basic template logic, just to show you how things work *}
{include file=#headerFile#}
{include file=$viewFile}
{include file=#footerFile#}