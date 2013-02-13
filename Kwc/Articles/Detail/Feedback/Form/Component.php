<?php
class Kwc_Articles_Detail_Feedback_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Feedback');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Grid';
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->article_id = $this->getData()->parent->parent->row->id;
        $row->user_id = Kwf_Registry::get('userModel')->getAuthedUser()->id;
        $row->date = date('Y-m-d H:i:s', time());
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);
        $tpl = new Kwf_Mail_Template($this);
        $article = $this->getData()->parent->parent->row;
        $author = $article->getParentRow('Author');
        if ($author->feedback_email) {
            $user = $row->getParentRow('User');
            $tpl->text = $row->text;
            $tpl->article = $article;
            $tpl->user = $user;
            $tpl->addTo($author->feedback_email, "$author->firstname $author->lastname");
            $tpl->setFrom($user->email, "$user->firstname $user->lastname");
            $tpl->setSubject($this->getData()->trlKwf('Feedback to article ') . $article->title);
            $tpl->send();
        }
    }
}
