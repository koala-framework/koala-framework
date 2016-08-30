<?php
if ($this->data->url) {
    echo '<a class="'.$this->linkClass.'" href="' . htmlspecialchars($this->data->url) . '"';
    if($this->data->rel) { echo ' rel="' . htmlspecialchars($this->data->rel) . '"'; }
    if ($this->linkTitle) { echo ' title="' . htmlspecialchars($this->linkTitle) . '"'; }
    $attributes = $this->data->getLinkDataAttributes();
    foreach ($attributes as $k=>$i) {
        echo ' data-'.htmlspecialchars($k).'="' . htmlspecialchars($i) . '"';
    }
    echo '>';
}
