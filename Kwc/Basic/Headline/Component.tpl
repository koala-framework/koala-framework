<div class="<?=$this->rootElementClass?>">
    <div class="<?=$this->bemClass('componentAnchor')?>" id="<?=$this->row->component_id?>"></div>
    <<?=$this->headlineType['tag'] ?><? if ($this->headlineType['class']) { ?> class="<?=$this->bemClass('headline')?> <?=$this->bemClass($this->headlineType['class'])?>"<? } ?>>
        <span><?= $this->headline1 ?></span>
        <? if ($this->headline2) { ?><span class="<?=$this->bemClass('sub')?>"><?= $this->headline2 ?></span><? } ?>
    </<?=$this->headlineType['tag'] ?>>
</div>
