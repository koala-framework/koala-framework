<?php if ($this->hasContent($this->boxes['menuTop'])) { ?>
    menuTopHasContent
    <?=$this->component($this->boxes['menuTop'])?>
<?php } ?>

<?php if ($this->hasContent($this->boxes['menuMain'])) { ?>
    menuMainHasContent
    <?=$this->component($this->boxes['menuMain'])?>
<?php } ?>
