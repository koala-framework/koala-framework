<?php
if ($this->data->url_mail_txt) {
    echo Kwf_Util_HtmlSpecialChars::filter($this->data->url_mail_txt);
}
