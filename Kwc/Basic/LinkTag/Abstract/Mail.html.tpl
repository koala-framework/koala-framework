<?php
if ($this->data->url) {
    echo '<a href="' . htmlspecialchars($this->data->url) . '">';
} else {
    echo '<a>'; // hack, see commit message
}