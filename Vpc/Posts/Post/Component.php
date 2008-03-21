<?php
class Vpc_Posts_Post_Component extends Vpc_Abstract_Composite_Component
{
    private $_postNum = null;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_Posts_Model',
            'childComponentClasses' => array(
                'user' => 'Vpc_Posts_Post_UserDetail_Component'
            )
        ));
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->getTable()->find($this->getCurrentComponentKey())->current();
        $ret['content'] = $this->_replaceCodes($row->content);
        $ret['create_time'] = $row->create_time;
        $ret['postNum'] = $this->_postNum;
        $ret['editUrl'] = '';
        if ($this->mayEditPost()) {
            $ret['editUrl'] = $this->getParentComponent()->getPageFactory()
                ->getChildPageById('write')->getComponent()->getEditUrl($row->id);
        }
        return $ret;
    }

    public function setPostNum($postNum)
    {
        $this->_postNum = $postNum;
    }

    public function mayEditPost()
    {
        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
        if (!$authedUser) return false;

        $post = $this->getTable()->find($this->getCurrentComponentKey())->current();
        if ($authedUser->id == $post->user_id) {
            return true;
        }

        return false;
    }

    private function _replaceCodes($content)
    {
        $content = str_replace('[quote]', '<fieldset class="quote"><legend>Zitat</legend>', $content, $countOpened);

        $content = str_replace('[/quote]', '</fieldset>', $content, $closed);

        $content = preg_replace('/\[quote=(.+)\]/i',
            '<fieldset class="quote"><legend>Zitat von $1</legend>',
            $content,
            -1, $countOpenedPreg
        );

        $open = $countOpened + $countOpenedPreg;

        while ($open > $closed) {
            $content .= '</fieldset>';
            $closed++;
        }

        return $content;
    }
}
