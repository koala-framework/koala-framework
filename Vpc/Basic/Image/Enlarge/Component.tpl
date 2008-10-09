<?php
if ($this->smallImage && $this->smallImage['url'] && $this->url) {
?>
<a href="<?php echo $this->url ?>"
    rel="enlarge_<?= $this->width.'_'.$this->height; ?><? if ($this->fullSizeUrl) echo '_'.$this->fullSizeUrl; ?>"
    title="<?php echo $this->mailEncodeText(htmlspecialchars($this->comment)) ?>"
>
    <img src="<?php echo $this->smallImage['url'] ?>" 
    width="<?php echo $this->smallImage['width'] ?>" 
    height="<?php echo $this->smallImage['height'] ?>" />
    <div class="webZoom"></div>
</a>
<?php } ?>
