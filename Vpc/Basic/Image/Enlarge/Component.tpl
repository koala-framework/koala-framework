<?php
if ($this->smallImage && $this->smallImage['url'] && $this->url) {
?>
<a href="<?php echo $this->url ?>"
    rel="enlarge_<?php echo $this->width ?>_<?php echo $this->height ?>"
    title="<?php echo $this->comment ?>"
><img src="<?php echo $this->smallImage['url'] ?>" 
    width="<?php echo $this->smallImage['width'] ?>" 
    height="<?php echo $this->smallImage['height'] ?>" />
</a>
<?php } ?>
