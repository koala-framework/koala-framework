<?php
class Kwf_Iterator_Filter_Php extends Kwf_Iterator_Filter_FileExtension
{
    function __construct($iterator)
    {
        parent::__construct($iterator, 'php');
    }
}
