<div class="<?=$this->rootElementClass?>">
    <input type="hidden" value="<?=Kwf_Util_HtmlSpecialChars::filter(json_encode($this->config))?>" />
    <div class="<?php if(count($this->listItems) > 1) { ?>listWrapper<?php } else { ?>imageWrapper<?php } ?>">
        <?php foreach ($this->listItems as $child) { ?>
        <div class="<?=$child['class'];?>" style="<?=$child['style'];?>">
            <?=$this->component($child['data']);?>
        </div>
        <?php } ?>
    </div>
</div>
