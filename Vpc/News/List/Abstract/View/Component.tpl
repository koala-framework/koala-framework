<div class="<?=$this->cssClass?>">
<?php foreach ($this->news as $new) { ?>
    <div class="text">
        <?=$this->componentLink($new);?>
        <span class="publishDate"><?=$new->row->publish_date?></span>
        <p><?=$this->mailEncodeText($new->row->teaser)?></p>
    </div>
<?php } ?>
</div>