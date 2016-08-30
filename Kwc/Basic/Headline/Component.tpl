<div class="<?=$this->rootElementClass?>">
    <?php if ($this->showAnchor) { ?>
    <div class="<?=$this->bemClass('componentAnchor')?>" id="<?=$this->row->component_id?>"></div>
    <?php } ?>
    <<?=$this->headlineType['tag'] ?> class="<?=$this->bemClass('headline')?> <?php if ($this->headlineType['class']) { ?><?=$this->bemClass($this->headlineType['class'])?><?php } ?>">
        <span><?= $this->headline1 ?></span>
        <?php if ($this->headline2) { ?><span class="<?=$this->bemClass('sub')?>"><?= $this->headline2 ?></span><?php } ?>
    </<?=$this->headlineType['tag'] ?>>
</div>
