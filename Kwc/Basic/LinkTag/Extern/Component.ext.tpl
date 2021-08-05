<?php
if ($this->data->url) {
    echo '<a class="'.$this->rootElementClass.'" href="' . Kwf_Util_HtmlSpecialChars::filter($this->data->url) . '"';
    $attrs = $this->data->getLinkDataAttributes();
    if (isset($attrs['kwc-popup']) && $attrs['kwc-popup'] == 'blank') {
        echo " target=\"_blank\"";
    }
    if($this->rel) { echo ' rel="' . Kwf_Util_HtmlSpecialChars::filter($this->rel) . '"'; }
    echo '>';
}
