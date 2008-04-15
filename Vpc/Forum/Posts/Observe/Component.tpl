{if $component.isObserved}
    <a class="observed" href="{$component.observeUrl}">Thema beobachten</a>
{else}
    <a class="notObserved" href="{$component.observeUrl}">Thema beobachten</a>
{/if}