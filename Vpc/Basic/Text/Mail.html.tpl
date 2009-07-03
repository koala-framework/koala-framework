<div class="<?=$this->cssClass?>">
<?php
foreach ($this->contentParts as $part) {
    echo is_string($part) ? $this->mailEncodeText($part) : $this->component($part['component']);
}
?>
</div>