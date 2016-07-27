<div class="<?=$this->rootElementClass?>">
    <div class="text">
        <h2><?=$this->component($this->link)?><?=$this->mailEncodeText($this->title)?></a></h2>
        <?php if ($this->teaser) { ?>
        <p><?=$this->mailEncodeText($this->teaser)?></p>
        <?php } ?>
    </div>
    <div class="image"><?=$this->component($this->link)?><?=$this->component($this->image)?></a></div>
</div>
