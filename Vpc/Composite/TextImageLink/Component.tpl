<div class="<?=$this->cssClass?>">
    <div class="text">
        <h2><?=$this->component($this->link)?><?=$this->mailEncodeText($this->title)?></a></h2>
        <p><?=$this->mailEncodeText($this->teaser)?></p>
    </div>
    <?=$this->component($this->link)?><?=$this->component($this->image)?></a>
</div>