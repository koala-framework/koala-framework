<?php
if ($this->data->url) {
    echo '<a href="*redirect*' . htmlspecialchars($this->data->url) . '*">';
}