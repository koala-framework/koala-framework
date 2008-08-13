<div class="<?=$this->cssClass?>">

    <? if ($this->row->nickname != '') { ?>
    <h3><?=trlVps('Name for Forum')?>:</h3>
    <?=htmlspecialchars($this->row->nickname)?>
    <? } ?>

    <? if ($this->row->location != '') { ?>
    <h3><?=trlVps('Place of living')?>:</h3>
    <?=htmlspecialchars($this->row->location)?>
    <? } ?>

    <? if ($this->row->signature != '') { ?>
    <h3><?=trlcVps('forum', 'Signature')?>:</h3>
    <?=nl2br(htmlspecialchars($this->row->signature))?>
    <? } ?>

    <? if ($this->row->description_short != '') { ?>
    <h3><?=trlVps('Short description')?>:</h3>
    <?=nl2br(htmlspecialchars($this->row->description_short))?>
    <? } ?>

    <? if($this->showLastPosts) { ?>
    <h3><?=trlVps('Last Posts')?>:</h3>
    <ul>
    <?php foreach ($this->lastPosts as $lastPost) { ?>
    <li><?= $this->componentLink($lastPost, $lastPost->linktext) ?></li>
    <?php } ?>
    </ul>
    <?php } ?>
</div>