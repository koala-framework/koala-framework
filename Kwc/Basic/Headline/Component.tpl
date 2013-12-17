<div class="<?=$this->cssClass?>">
    <<?=$this->headlineType ?>>
        <?= $this->headline1 ?>
        <? if ($this->headline2) { ?><span class="sub"><?= $this->headline2 ?></span><? } ?>
    </<?=$this->headlineType ?>>
</div>
