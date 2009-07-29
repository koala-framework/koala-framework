<div class="<?=$this->cssClass?>">
    <?=$this->component($this->linkTag)?>

        <?=$this->image($this->data, 'default', '', $this->imgCssClass)?>
        <div class="webZoom"></div>

    <?=$this->ifHasContent($this->linkTag)?>
    </a>
    <?=$this->ifHasContent()?>
</div>