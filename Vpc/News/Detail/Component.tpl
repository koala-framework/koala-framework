<div class="<?=$this->cssClass?>">
    <h2><?= $this->mailEncodeText($this->news['title']) ?></h2>
    <?=$this->component($this->content) ?>
</div>