<?php
class Kwf_Controller_Front_Component extends Kwf_Controller_Front
{
    protected function _getDefaultWebRouter()
    {
        return new Kwf_Controller_Router('admin');
    }
}
