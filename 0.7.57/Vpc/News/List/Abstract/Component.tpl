{component component=$component.paging}
{foreach from=$component.news item=news}
    <a href="{$news.href}">{$news.title}</a>
    <p>{$news.teaser}<p>
    {$news.publish_date}
    <br /><br />
{/foreach}
