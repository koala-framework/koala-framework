<?php
if ($this->data->url) {
    echo '<a class="'.$this->rootElementClass.'" href="' . htmlspecialchars($this->data->url) . '"';
    if($this->data->rel) { echo ' rel="' . htmlspecialchars($this->data->rel) . '"'; }
    $attributes = $this->data->getLinkDataAttributes();
    foreach ($attributes as $k=>$i) {
        echo ' data-'.htmlspecialchars($k).'="' . htmlspecialchars($i) . '"';
    }
    echo '>';
}
