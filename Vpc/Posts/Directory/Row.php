<?php
class Vpc_Posts_Directory_Row extends Vpc_Abstract_Composite_Row
{
    public function __toString()
    {
        return $this->id;
    }
}
