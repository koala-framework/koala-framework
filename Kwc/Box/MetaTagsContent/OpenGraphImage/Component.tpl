<?php if ($this->imageUrl) { ?>
<meta property="og:image" content="<?=$this->imageUrl?>" />
<?php } ?>
<?php if ($this->width) { ?>
<meta property="og:image:width" content="<?=$this->width?>" />
<?php } ?>
<?php if ($this->height) { ?>
<meta property="og:image:height" content="<?=$this->height?>" />
<?php } ?>
