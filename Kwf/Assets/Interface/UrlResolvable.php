<?php
interface Kwf_Assets_Interface_UrlResolvable
{
    public function toUrlParameter();
    public static function fromUrlParameter($class, $parameter);
}
