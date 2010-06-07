<div class="<?=$this->cssClass?>">
    <?=$this->component($this->linkTag)?>

        <?=$this->image($this->image, 'default', '', $this->imgCssClass)?>

    <?=$this->ifHasContent($this->linkTag)?>
    </a>
    <?=$this->ifHasContent()?>
</div>