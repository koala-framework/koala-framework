<h2>{$component.news.title}</h2>
{$component.news.publish_date}
{foreach from=$component.paragraphs item=paragraph}
    {component component=$paragraph}
{/foreach}
