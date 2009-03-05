<?php
class Vps_View_Helper_Dynamic
{
    public function dynamic($class)
    {
        $args = func_get_args();
        $class = array_shift($args);
        $serializedContent = serialize($args);
        return "{dynamic: $class }$serializedContent{/dynamic}";
    }
}
