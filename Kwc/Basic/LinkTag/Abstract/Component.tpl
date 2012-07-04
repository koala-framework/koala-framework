<?php
if ($this->data->url) {
    echo '<a class="'.$this->cssClass.'" href="' . htmlspecialchars($this->data->url) . '"';
    if($this->data->rel) { echo ' rel="' . htmlspecialchars($this->data->rel) . '"'; }
    echo '>';
}
