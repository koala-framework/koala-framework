<div class="<?=$this->cssClass?>">
    <?=$this->component($this->linkTag)?>

        <?=$this->image($this->data, 'default', '', $this->imgCssClass)?>
        
        <?=$this->ifHasContent($this->linkTag)?>
            <div class="webZoom"></div>
        <?=$this->ifHasContent()?>

    <?=$this->ifHasContent($this->linkTag)?>
    </a>
    <?=$this->ifHasContent()?>
</div>