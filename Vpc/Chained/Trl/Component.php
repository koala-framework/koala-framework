<?php
class Vpc_Chained_Trl_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['chained'] = array(
            'class' => 'Vpc_Chained_Trl_Generator',
            'component' => 'Vpc_Chained_Trl_Component'
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $data = $this->getData();
        $ret = $data->chained->getComponent()->getTemplateVars();
        $ret['chained'] = $data->chained;
        $ret['linkTemplate'] = self::getTemplateFile($data->chained->componentClass);

        $ret['componentClass'] = get_class($this);
        return $ret;
    }
}
