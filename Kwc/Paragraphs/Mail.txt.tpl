<?php
foreach ($this->paragraphs as $paragraph) {
    echo $this->component($paragraph['data']) . "\n";
}
?>