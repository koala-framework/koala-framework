<div class="<?=$this->cssClass?>">
<?php foreach ($this->news as $new) { ?>
    <div class="text">
        <?=$this->componentLink($new['detail']);?>
        <span class="publishDate"><?=$new['row']->publish_date?></span>
        <p><?=$new['row']->teaser?></p>
    </div>
<?php } ?>
</div>