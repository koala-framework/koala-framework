<?php
class Kwf_Component_Cache_ParentContent_RootAlternative_Box_Component extends Kwc_Basic_Empty_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'has_content';
        $ret['ownModel'] = 'Kwf_Component_Cache_ParentContent_RootAlternative_Box_Model';
        $ret['flags']['hasAlternativeComponent'] = true;
        return $ret;
    }

    public static function getAlternativeComponents($componentClass)
    {
        return array(
            'parentContent' => 'Kwc_Basic_ParentContent_Component',
        );
    }
    
    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        if ($parentData->componentId == 2) {
            return 'parentContent';
        } else {
            return false;
        }
    }

    public function hasContent()
    {
        return $this->getRow()->has_content;
    }
}
