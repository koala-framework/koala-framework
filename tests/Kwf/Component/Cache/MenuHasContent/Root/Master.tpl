<? if($this->hasContent($this->boxes['menuTop'])) { ?>
    menuTopHasContent
    <?=$this->component($this->boxes['menuTop'])?>
<? } ?>

<? if($this->hasContent($this->boxes['menuMain'])) { ?>
    menuMainHasContent
    <?=$this->component($this->boxes['menuMain'])?>
<? } ?>
