<?php
if ($this->smallImage && $this->smallImage['height'] && $this->url) {
    $topMargin = floor(($this->thumbMaxHeight - $this->smallImage['height']) / 2);
?>
<a href="<?php echo $this->url ?>"
    rel="enlarge_<?php echo $this->width ?>_<?php echo $this->height ?>"
    title="<?php echo $this->comment ?>"
><img src="<?php echo $this->smallImage['url'] ?>" 
    style="margin-top:<?php echo $topMargin ?>px;" 
    width="<?php echo $this->smallImage['width'] ?>" 
    height="<?php echo $this->smallImage['height'] ?>" />
</a>
<?php } ?>
