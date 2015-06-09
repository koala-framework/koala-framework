<?php
class Kwf_View_Twig_FilesystemLoader extends Twig_Loader_Filesystem
{
    //don't validate, we can trust our file names
    protected function validateName($name)
    {
    }
}
