<? if ($this->hasContent($this->boxes['box1'])) { ?>
    box1
    <?=$this->component($this->boxes['box1']);?>
<? } ?>

<? if ($this->hasContent($this->boxes['box2'])) { ?>
    box2
    <?=$this->component($this->boxes['box2']);?>
<? } ?>

<?=$this->component($this->component)?>
