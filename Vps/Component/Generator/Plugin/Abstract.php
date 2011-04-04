<?php
abstract class Vps_Component_Generator_Plugin_Abstract extends Vps_Component_Abstract
{
    /**
     * @var Vps_Component_Generator_Abstract
     */
    protected $_generator;
    public function __construct(Vps_Component_Generator_Abstract $generator)
    {
        $this->_generator = $generator;
        parent::__construct();
    }
}
