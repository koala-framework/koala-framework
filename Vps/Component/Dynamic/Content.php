<?php
/**
 * Gibt den $content 1:1 aus.
 *
 * macht natürlich nur sinn wenn getContent mit irgendwelchen bedingungen
 * überschrieben wird
 */
class Vps_Component_Dynamic_Content extends Vps_Component_Dynamic_Abstract
{
    protected $_content;
    public function setArguments($content)
    {
        $this->_content = $content;
    }
    public function getContent()
    {
        return $this->_content;
    }
}
