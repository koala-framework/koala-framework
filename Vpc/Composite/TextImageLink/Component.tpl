<div class="<?=$this->cssClass?>">
    <div class="text">
        <h2><?=$this->component($this->link)?><?=$this->mailEncodeText($this->title)?></a></h2>
        <? if ($this->teaser) { ?>
        <p><?=$this->mailEncodeText($this->teaser)?></p>
        <? } ?>
    </div>
    <div class="image"><?=$this->component($this->link)?><?=$this->component($this->image)?></a></div>
</div>
