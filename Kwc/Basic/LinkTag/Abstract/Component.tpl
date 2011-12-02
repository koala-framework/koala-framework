<?php
if ($this->data->url) {
    echo '<a href="' . htmlspecialchars($this->data->url) . '"';
    if($this->data->rel) { echo ' rel="' . htmlspecialchars($this->data->rel) . '"'; }
    echo '>';
}
