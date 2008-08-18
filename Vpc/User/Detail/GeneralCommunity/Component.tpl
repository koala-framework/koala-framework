<div class="<?=$this->cssClass?>">

    <div class="nickname"><?=htmlspecialchars($this->row->nickname)?></div>
    
    <?php
    if ($this->rating) {
        echo $this->component($this->rating);
    }
    if ($this->avatar) {
        echo $this->component($this->avatar);
    }
    ?>

    <h3><?=trlVps('Member since')?></h3>
    <?=$this->date($this->row->created)?>

    <h3><?=trlVps('Latest online')?></h3>
    <?=$this->timestamp($this->row->last_login)?>

    <? if ($this->row->location != '') { ?>
    <h3><?=trlVps('Place of living')?>:</h3>
    <?=htmlspecialchars($this->row->location)?>
    <? } ?>

    <? if ($this->row->description_short != '') { ?>
    <h3><?=trlVps('Short description')?>:</h3>
    <?=nl2br(htmlspecialchars($this->row->description_short))?>
    <? } ?>

    <? if ($this->showLastPosts) { ?>
    <h3><?=trlVps('Last Posts')?>:</h3>
    <ul>
        <?php foreach ($this->lastPosts as $lastPost) { ?>
            <li><?= $this->componentLink($lastPost, $lastPost->linktext) ?></li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>