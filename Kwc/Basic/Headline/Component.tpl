<div class="<?=$this->rootElementClass?>">
    <? if ($this->showAnchor) { ?>
    <div class="<?=$this->bemClass('componentAnchor')?>" id="<?=$this->row->component_id?>"></div>
    <? } ?>
    <<?=$this->headlineType['tag'] ?> class="<?=$this->bemClass('headline')?> <? if ($this->headlineType['class']) { ?><?=$this->bemClass($this->headlineType['class'])?><? } ?>">
        <span><?= $this->headline1 ?></span>
        <? if ($this->headline2) { ?><span class="<?=$this->bemClass('sub')?>"><?= $this->headline2 ?></span><? } ?>
    </<?=$this->headlineType['tag'] ?>>
</div>
