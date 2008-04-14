{if $component.isObserved}
    <a class="observed" href="{$component.observeUrl}">beobachten</a>
{else}
    <a class="notObserved" href="{$component.observeUrl}">beobachten</a>
{/if}