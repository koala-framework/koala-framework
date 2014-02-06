<?php
class Kwf_DateTime extends Kwf_Date
{
    public function format($format = 'Y-m-d H:i:s', $language = null)
    {
        return parent::format($format, $language);
    }
}
