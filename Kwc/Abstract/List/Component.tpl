<div class="<?=$this->rootElementClass?>" data-width="100%">
    <?php foreach ($this->listItems as $child) { ?>
    <?=$child['preHtml']?>
    <div class="<?=$child['class'];?>" style="<?=$child['style'];?>">
        <?=$this->component($child['data']);?>
    </div>
    <?=$child['postHtml']?>
    <?php } ?>
</div>
