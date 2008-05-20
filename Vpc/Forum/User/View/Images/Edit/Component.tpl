<div class="vpcUserEdit">
    <h1>{trlVps text="Account - Images"}</h1>

    {if $component.sent != 3}
        {foreach from=$component.images item=i}
        <div class="imageContainer {cycle values="left,middle,right,last"}">
            {component component=$i.image}
            <p>{if !$i.comment}&nbsp;{/if}{$i.comment|truncate:13:'...':true}</p>
            <a class="close" href="{$i.delete}">löschen</a>
        </div>
        {/foreach}
        <div class="clear"></div>
    {/if}

    {include file=$component.formTemplate}
</div>