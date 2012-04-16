<?php
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

        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Mail/PreviewWindow.js';
        $ret['plugins']['placeholders'] = 'Kwc_Mail_PlaceholdersPlugin';
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
        }
        return $ret;
    }

    public function createMail(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null)
    {
        $mail = parent::createMail($recipient, $data, $toAddress, $format);
        if ($this->getRow()->from_email) {
            $mail->setFrom($this->getRow()->from_email, $this->getRow()->from_name);
        }
        if ($this->getRow()->reply_email) {
            $mail->setReplyTo($this->getRow()->reply_email);
        }
        return $mail;
    }

    protected function _getSubject()
    {
        return $this->getRow()->subject;
    }
}
