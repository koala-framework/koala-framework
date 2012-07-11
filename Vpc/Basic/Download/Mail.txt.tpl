<?php
    echo $this->component($this->downloadTag);
    echo $this->mailEncodeText($this->infotext);
    echo $this->ifHasContent($this->downloadTag);
    if ($this->filesize) {
        echo "  ".$this->fileSize($this->filesize);
    }
    echo $this->ifHasContent();
?>