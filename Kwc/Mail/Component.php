<?php
/**
 * Used for sending editable mails, subject, from etc are stored in model
 */
class Kwc_Mail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Paragraphs_Component'
        );

        $sender = Kwf_Mail::getSenderFromConfig();
        $ret['default'] = array(
            'from_email' => $sender['address'],
            'from_name' => $sender['name']
        );
        $ret['editFrom'] = true;
        $ret['editReplyTo'] = true;

        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Mail/PreviewWindow.js';
        $ret['assetsAdmin']['dep'][] = 'ExtWindow';
        $ret['ownModel'] = 'Kwc_Mail_Model';
        $ret['componentName'] = 'Mail';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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

    public function createMail(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null, $addViewTracker = true)
    {
        $mail = parent::createMail($recipient, $data, $toAddress, $format, $addViewTracker);
        if ($this->getRow()->from_email && $this->_getSetting('editFrom')) {
            $mail->setFrom($this->getRow()->from_email, $this->getRow()->from_name);
        }
        if ($this->getRow()->reply_email && $this->_getSetting('editReplyTo')) {
            $mail->setReplyTo($this->getRow()->reply_email);
        }
        return $mail;
    }

    protected function _getSubject()
    {
        return $this->getRow()->subject;
    }
}
