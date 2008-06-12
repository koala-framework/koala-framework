<?=$this->component($this->paging)?>
<?php foreach ($this->news as $n) { ?>
    <?=$this->component($n)?>
<?php } ?>
<?=$this->component($this->paging)?>
