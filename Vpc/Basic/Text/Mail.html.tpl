<?php
foreach ($this->contentParts as $part) {
    echo is_string($part) ? $this->mailFormat($part, $this->styles) : $this->component($part['component']);
}
?>