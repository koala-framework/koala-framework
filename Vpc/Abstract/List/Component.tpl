<div class="<?=$this->cssClass?>">
<?php
foreach ($this->children as $child) {
    echo $this->component($child);
}
?>
</div>