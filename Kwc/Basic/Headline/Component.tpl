<div class="<?=$this->cssClass?>">
    <<?=$this->headlineType['tag'] ?> class="<?=$this->headlineType['class']?>">
        <span><?= $this->headline1 ?></span>
        <? if ($this->headline2) { ?><span class="sub"><?= $this->headline2 ?></span><? } ?>
    </<?=$this->headlineType['tag'] ?>>
</div>
