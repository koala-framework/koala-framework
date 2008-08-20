<?php
class Vpc_Forum_Directory_Row extends Vpc_Row
{
    public function __toString()
    {
        return $this->name;
    }
}
