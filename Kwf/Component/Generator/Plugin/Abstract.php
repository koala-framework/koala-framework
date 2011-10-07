<?php
abstract class Kwf_Component_Generator_Plugin_Abstract extends Kwf_Component_Abstract
{
    /**
     * @var Kwf_Component_Generator_Abstract
     */
    protected $_generator;
    public function __construct(Kwf_Component_Generator_Abstract $generator)
    {
        $this->_generator = $generator;
        parent::__construct();
    }
}
