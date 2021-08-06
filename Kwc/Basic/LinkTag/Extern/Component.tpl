<?php
if ($this->data->url) {
    echo '<a class="'.$this->linkClass.'" href="' . Kwf_Util_HtmlSpecialChars::filter($this->data->url) . '"';
    if($this->rel) { echo ' rel="' . Kwf_Util_HtmlSpecialChars::filter($this->rel) . '"'; }
    if ($this->linkTitle) { echo ' title="' . Kwf_Util_HtmlSpecialChars::filter($this->linkTitle) . '"'; }
    $attributes = $this->data->getLinkDataAttributes();
    foreach ($attributes as $k=>$i) {
        echo ' data-'.Kwf_Util_HtmlSpecialChars::filter($k).'="' . Kwf_Util_HtmlSpecialChars::filter($i) . '"';
    }
    echo '>';
}
