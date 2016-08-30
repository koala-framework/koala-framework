<div class="<?=$this->rootElementClass?>" id="<?=$this->data->componentId?>">
    <?php if ($this->hasContent) { ?>
    <ul class="links">
        <?php foreach ($this->links as $link) { ?>
            <li><?=$this->componentLink($link['component'], $this->data->trlStaticExecute($link['text']))?></li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>
