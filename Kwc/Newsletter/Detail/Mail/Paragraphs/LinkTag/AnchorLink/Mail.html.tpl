<?php
if ($this->data->url) {
    echo '<a href="' . Kwf_Util_HtmlSpecialChars::filter($this->data->url) . '">';
} else {
    echo '<a>'; // hack, see Kwc_Basic_LinkTag_Abstract_Component
}
