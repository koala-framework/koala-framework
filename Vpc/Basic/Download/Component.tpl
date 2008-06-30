<?php
echo $this->component($this->downloadTag);
echo ' ';
echo $this->infotext;
if ($this->filesize) {
    echo ' (' . $this->fileSize($this->filesize) . ')';
}
?></a>