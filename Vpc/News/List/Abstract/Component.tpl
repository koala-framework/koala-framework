<? if($this->paging) { ?>
    <?=$this->component($this->paging)?>
<? } ?>
<?=$this->component($this->view)?>
<? if($this->paging) { ?>
    <?=$this->component($this->paging)?>
<? } ?>
