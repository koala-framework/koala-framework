<?php
if ($this->imageUrl) {
    echo '<a class="' . $this->bemClass('kwcEnlargeTag') . ' ' . $this->imagePage->getLinkClass() . '" href="' . Kwf_Util_HtmlSpecialChars::filter($this->imageUrl) . '"';
    if ($this->imagePage->rel) { echo ' rel="' . Kwf_Util_HtmlSpecialChars::filter($this->imagePage->rel) . '"'; }
    if ($this->linkTitle) { echo ' title="' . Kwf_Util_HtmlSpecialChars::filter($this->linkTitle) . '"'; }
    $attributes = $this->imagePage->getLinkDataAttributes();
    foreach ($attributes as $k=>$i) {
        echo ' data-'.Kwf_Util_HtmlSpecialChars::filter($k).'="' . Kwf_Util_HtmlSpecialChars::filter($i) . '"';
    }
    echo '>';
}
