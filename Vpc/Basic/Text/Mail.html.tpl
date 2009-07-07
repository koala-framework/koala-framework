<div>
<?php
foreach ($this->contentParts as $part) {
    echo is_string($part) ? $part : $this->component($part['component']);
}
?>
</div>