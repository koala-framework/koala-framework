<?php
class Kwc_Basic_DownloadTag_Trl_Component extends Kwc_Basic_LinkTag_Abstract_Trl_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['download'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => $masterComponentClass
        );
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['dataClass'] = 'Kwc_Basic_DownloadTag_Trl_Data';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['url'] = $this->getDownloadUrl();
        return $ret;
    }

    public function getDownloadUrl()
    {
        return $this->getData()->url;
    }

    public function getFilesize()
    {
        $fRow = $this->getFileRow()->getParentRow('File');
        if (!$fRow) return null;
        return $fRow->getFileSize();
    }

    public function getFileRow()
    {
        if ($this->getRow()->own_download) {
            return $this->getData()->getChildComponent('-download')->getComponent()->getFileRow();
        } else {
            return $this->getData()->chained->getComponent()->getFileRow();
        }
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        $cls = $c->chained->componentClass;
        $cls = strpos($cls, '.') ? substr($cls, 0, strpos($cls, '.')) : $cls;
        return call_user_func(array($cls, 'getMediaOutput'), $c->chained->componentId, $type, $c->chained->componentClass);
    }
}
