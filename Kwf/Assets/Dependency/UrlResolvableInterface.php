<?php
interface Kwf_Assets_Dependency_UrlResolvableInterface
{
    public function toUrlParameter();
    public static function fromUrlParameter($class, $parameter);
}
