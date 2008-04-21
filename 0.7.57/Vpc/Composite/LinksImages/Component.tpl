<div class="vpcCompositeLinksImages">
    {foreach from=$component.children item=child}
        {component component=$child}
    {/foreach}
    <div class="clear"></div>
</div>
