<div class="<?=$this->cssClass?>">
<? if($this->paging->hasContent()) { ?>
    <?=$this->component($this->paging)?>
<? } ?>
<?php foreach ($this->news as $new) { ?>
    <div class="text">
        <?=$this->componentLink($new);?>
        <span class="publishDate"><?=$new->row->publish_date?></span>
        <p><?=$this->mailEncodeText($new->row->teaser)?></p>
    </div>
<?php } ?>
<? if($this->paging->hasContent()) { ?>
    <?=$this->component($this->paging)?>
<? } ?>
</div>