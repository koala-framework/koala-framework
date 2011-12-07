<?php
if ($this->data->url) {
    echo '<a href="*redirect*' . $this->data->url . '*">';
} else {
    echo '<a>'; // hack, see commit message
}