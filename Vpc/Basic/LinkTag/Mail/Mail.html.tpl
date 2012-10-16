<?php
if ($this->data->url_mail_html) {
    echo '<a href="' . htmlspecialchars($this->data->url_mail_html) . '">';
}
