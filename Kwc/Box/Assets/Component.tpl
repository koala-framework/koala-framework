<!-- <?=$this->kwfUp?>assets -->
<?php foreach ($this->assetsPackages as $package) { ?>
<?= $this->assets($package, $this->language, $this->subroot) ?>
<?php } ?>
<!-- /<?=$this->kwfUp?>assets -->
