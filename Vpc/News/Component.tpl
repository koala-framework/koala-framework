{foreach from=$component.news item=news}
<a href="{$news.url}">{$news.title}</a>
<p>{$news.teaser}<p>
{$news.date}
<br /><br />
{/foreach}
