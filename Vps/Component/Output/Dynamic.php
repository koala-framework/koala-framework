<?php
class Vps_Component_Output_Dynamic extends Vps_Component_Output_Abstract
{
    public function render($component, $config, $view)
    {
        $class = $config[0];
        $args = unserialize(base64_decode($config[1]));

        $dynamic = new $class();
        call_user_func_array(array($dynamic, 'setArguments'), $args);
        $dynamic->setInfo(unserialize(base64_decode($config[2])));
        return $dynamic->getContent();
    }
}
