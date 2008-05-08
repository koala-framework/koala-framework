<?php
if ($this->downloadTag['url']) {
    $this->component($this->downloadTag);
    echo $this->infotext;
    if ($this->downloadTag['filesize'] > 0) {
        echo '(' . $this->fileSize($this->downloadTag['filesize']) . ')';
    }
}