<?php
/**
 * Used for sending editable mails, subject, from etc are stored in model
 */
class Kwc_Mail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['content'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Paragraphs_Component'
        );

        $ret['editFrom'] = true;
        $ret['editReplyTo'] = true;
        $ret['editReturnPath'] = false;

        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Mail/PreviewWindow.js';
        $ret['assetsAdmin']['dep'][] = 'ExtWindow';
        $ret['ownModel'] = 'Kwc_Mail_Model';
        $ret['componentName'] = 'Mail';
        $ret['flags']['skipPagesMeta'] = true;

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $c = $this->getData()->getChildComponent('-content');
        if ($c) {
            $ret['content'] = $c;
        }
        return $ret;
    }

    public function getHtmlStyles()
    {
        $ret = parent::getHtmlStyles();

        // Hack für Tests, weil da der statische getStylesArray-Aufruf nicht funktioniert
        $contentComponent = $this->getData()->getChildComponent('-content');
        if ($contentComponent &&
            is_instance_of($contentComponent->componentClass, 'Kwc_Paragraphs_Component')
        ) {
            foreach (Kwc_Basic_Text_StylesModel::getStylesArray() as $tag => $classes) {
                foreach ($classes as $class => $style) {
                    $ret[] = array(
                        'tag' => $tag,
                        'class' => $class,
                        'styles' => $style['styles']
                    );
                }
            }
            foreach (Kwf_Model_Abstract::getInstance('Kwc_Basic_Text_StylesModel')->getMasterStyles() as $style) {
                $styles = array();
                if (preg_match_all('/([a-z-]+): +([^;]+);/', $style['styles'], $m)) {
                    foreach (array_keys($m[0]) as $i) {
                        $styles[$m[1][$i]] = $m[2][$i];
                    }
                }
                $ret[] = array(
                    'tag' => $style['tagName'],
                    'class' => $style['className'],
                    'styles' => $styles
                );
            }
        }
        return $ret;
    }

    protected function _getSubject()
    {
        return $this->getRow()->subject;
    }

    protected function _getFromEmail()
    {
        $ret = parent::_getFromEmail();
        if ($this->_getSetting('editFrom') && $this->getRow()->from_email) {
            $ret = $this->getRow()->from_email;
        }
        return $ret;
    }

    protected function _getFromName()
    {
        $ret = parent::_getFromName();
        if ($this->_getSetting('editFrom') && $this->getRow()->from_name) {
            $ret = $this->getRow()->from_name;
        }
        return $ret;
    }

    protected function _getReplyTo()
    {
        $ret = parent::_getReplyTo();
        if ($this->_getSetting('editReplyTo') && $this->getRow()->reply_email) {
            $ret = $this->getRow()->reply_email;
        }
        return $ret;
    }

    protected function _getReturnPath()
    {
        $ret = parent::_getReturnPath();
        if ($this->_getSetting('editReturnPath') && $this->getRow()->return_path) {
            $ret = $this->getRow()->return_path;
        }
        return $ret;
    }

    public function getDefaultFromEmail()
    {
        return parent::_getFromEmail();
    }

    public function getDefaultFromName()
    {
        return parent::_getFromName();
    }

    public function getDefaultReplyTo()
    {
        return parent::_getReplyTo();
    }

    public function getDefaultReturnPath()
    {
        return parent::_getReturnPath();
    }
}
