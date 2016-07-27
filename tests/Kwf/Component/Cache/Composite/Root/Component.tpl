<?php
foreach($this->keys as $k) {
    if ($this->hasContent($this->$k)) {
        echo $this->component($this->$k);
    }
}
?>
