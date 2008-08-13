<div class="<?=$this->cssClass?>">
    <h3><?=trlVps('Name for Forum')?>:</h3>
    <?=htmlspecialchars($this->row->nickname)?>

    <h3><?=trlVps('Place of living')?>:</h3>
    <?=htmlspecialchars($this->row->location)?>

    <h3><?=trlVps('Signature')?>:</h3>
    <?=nl2br(htmlspecialchars($this->row->signature))?>

    <h3><?=trlVps('Short description')?>:</h3>
    <?=nl2br(htmlspecialchars($this->row->description_short))?>

    <h3><?=trlVps('Last Posts')?>:</h3>
    <ul>
    <?php foreach ($this->lastPosts as $lastPost) { ?>
    <li><?= $this->componentLink($lastPost, $lastPost->linktext) ?></li>
    <?php } ?>
    </ul>
</div>