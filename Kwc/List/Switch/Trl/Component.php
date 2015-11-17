<?php
class Kwc_List_Switch_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['itemPages']['class'] = 'Kwc_List_Switch_Trl_ItemPageGenerator';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        foreach ($ret['listItems'] as &$item) {
            $item['largePage'] = $this->getData()
                ->getChildComponent(array('id' => '_'.$item['data']->id, 'ignoreVisible'=>true));
        }
        return $ret;
    }

    public function getDefaultItemPage()
    {
        return $this->getData()
            ->getChildComponent(array('generator'=>'itemPages', 'limit'=>1));
    }

    public function getLargeComponent($itemPageComponent)
    {
        return $this->getData()
            ->getChildComponent(array('id' => '-'.$itemPageComponent->id, 'ignoreVisible'=>true))
            ->getChildComponent('-large');
    }

    public function getPreviewComponent($itemPageComponent)
    {
        return $this->getData()
            ->getChildComponent(array('id' => '-'.$itemPageComponent->id, 'ignoreVisible'=>true));
    }
}
