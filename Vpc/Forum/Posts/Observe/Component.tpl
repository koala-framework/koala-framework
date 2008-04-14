{if $component.isObserved}
    <a href="{$component.observeUrl}">wird momentan beobachtet</a>
{else}
    <a href="{$component.observeUrl}">wird nicht beobachtet</a>
{/if}