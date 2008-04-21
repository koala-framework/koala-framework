{foreach from=$component.news item=news}
    <div>
        <a href="{$news.href}">{$news.title}</a>
    </div>
{/foreach}
