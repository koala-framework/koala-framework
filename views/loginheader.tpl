<? if ($this->untagged) { ?>
<div class="untagged">ACHTUNG: ungetaggt</div>
<? } ?>
<?php if($this->image) { ?>
    <img src="<?= $this->image ?>" width="<?= $this->imageSize['width'] ?>" height="<?= $this->imageSize['height'] ?>" />
<?php } else { ?>
    <h1><?= $this->application['name'] . ' ' . trlVps('Version') . ' ' . $this->application['version'] ?></h1>
<?php } ?>