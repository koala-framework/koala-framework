<?php
echo $this->component($this->downloadTag);
echo ' ';
echo $this->infotext;
echo '</a>';
if ($this->filesize) {
    echo ' <span>(' . $this->fileSize($this->filesize) . ')</span>';
}
?>