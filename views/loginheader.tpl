<?php if ($this->untagged) { ?>
<div class="untagged"><?=trlKwf('WARNING: untagged')?></div>
<?php } ?>
<?php if($this->image) { ?>
    <img src="<?= $this->image ?>" width="<?= $this->imageSize['width'] ?>" height="<?= $this->imageSize['height'] ?>" />
<?php } else { ?>
    <h1><?= $this->application['name'] ?></h1>
<?php } ?>
