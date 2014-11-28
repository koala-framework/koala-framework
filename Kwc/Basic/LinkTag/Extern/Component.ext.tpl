<?php
if ($this->data->url) {
    echo '<a class="'.$this->cssClass.'" href="' . htmlspecialchars($this->data->url) . '"';
    if ($this->data->rel == 'popup_blank') {
        echo " target=\"_blank\"";
    }
    echo '>';
}
