<?php
if ($this->data->url) {
    echo '<a href="' . htmlspecialchars($this->data->url) . '">';
} else {
    echo '<a>'; // hack, see Kwc_Basic_LinkTag_Abstract_Component
}
