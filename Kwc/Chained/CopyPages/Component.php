<?php
class Kwc_Chained_CopyPages_Component extends Kwc_Chained_CopyTarget_Component
{
    public static function getSettings($categoryComponentClass = 'Kwc_Root_Category_Component')
    {
        $ret = parent::getSettings($categoryComponentClass);

        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $pageGenerator = Kwc_Chained_Cc_Component::createChainedGenerator($categoryComponentClass, 'page');

        $ret['generators']['target'] = $pageGenerator;
        $ret['generators']['target']['class'] = 'Kwc_Chained_CopyPages_TargetGenerator';
        $ret['generators']['target']['inherit'] = false;

        $ret['generators']['page'] = $pageGenerator;
        $ret['generators']['page']['class'] = 'Kwc_Chained_CopyTarget_PagesGenerator';

        $ret['flags']['hasAllChainedByMaster'] = true;
        return $ret;
    }

    public function getTargetComponent()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getRow()->target,
            array(
                'subroot' => $this->getData()
            )
        );
    }
}
