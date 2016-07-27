<?php
    foreach ($this->listItems as $child) {
        echo $this->component($child['data']);
    }
?>
