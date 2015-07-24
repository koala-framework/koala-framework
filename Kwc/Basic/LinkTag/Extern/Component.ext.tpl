<?php
if ($this->data->url) {
    echo '<a class="'.$this->rootElementClass.'" href="' . htmlspecialchars($this->data->url) . '"';
    $attrs = $this->data->getLinkDataAttributes();
    if (isset($attrs['kwc-popup']) && $attrs['kwc-popup'] == 'blank') {
        echo " target=\"_blank\"";
    }
    echo '>';
}
