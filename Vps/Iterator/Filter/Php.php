<?php
class Vps_Iterator_Filter_Php extends Vps_Iterator_Filter_FileExtension
{
    function __construct($iterator)
    {
        parent::__construct($iterator, 'php');
    }
}
