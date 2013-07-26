<?php
    echo $this->component($this->downloadTag);
    echo $this->mailEncodeText($this->infotext);
    if ($this->hasContent($this->downloadTag)) {
        if ($this->filesize) {
            echo "  ".$this->fileSize($this->filesize);
        }
    }
?>