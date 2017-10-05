<?php
if ($this->data->url) {
    echo '<a href="' . Kwf_Util_HtmlSpecialChars::filter($this->data->url) . '">';
} else {
    echo '<a>'; // hack, see commit message
}
