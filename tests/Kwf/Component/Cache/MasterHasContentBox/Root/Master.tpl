<?php if ($this->hasContent($this->boxes['box1'])) { ?>
    box1
    <?=$this->component($this->boxes['box1']);?>
<?php } ?>

<?php if ($this->hasContent($this->boxes['box2'])) { ?>
    box2
    <?=$this->component($this->boxes['box2']);?>
<?php } ?>

<?=$this->component($this->component)?>
