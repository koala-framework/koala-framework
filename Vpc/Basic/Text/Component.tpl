{strip}
{foreach from=$component.contentParts item=part}
  {if is_string($part)}
    {$part}
  {else}
    {component component=$part}
  {/if}
{/foreach}
{/strip}