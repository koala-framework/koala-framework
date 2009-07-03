<?php
foreach ($this->contentParts as $part) {
    echo is_string($part) ? strip_tags($part) : $this->component($part['component']);
}
?>