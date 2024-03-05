<?php
if ($this->data->url_mail_html) {
    echo '<a href="' . Kwf_Util_HtmlSpecialChars::filter($this->data->url_mail_html) . '">';
}
