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
        $ret['content'] = self::replaceCodes($this->getContent($row));
        $ret['create_time'] = $row->create_time;
        $ret['postNum'] = $this->_postNum;
        $ret['editUrl'] = '';
        if ($this->mayEditPost()) {
            $ret['editUrl'] = $this->getParentComponent()->getPageFactory()
                ->getChildPageById('write')->getComponent()->getEditUrl($row->id);
        }
        return $ret;
    }

    public function getContent($row = null)
    {
        if (is_null($row)) {
            $row = $this->getTable()->find($this->getCurrentComponentKey())->current();
        }
        return $row->content;
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

    static public function replaceCodes($content)
    {
        // html entfernen
        $content = htmlspecialchars($content);

        // zitate
        $content = str_replace('[quote]', '<fieldset class="quote"><legend>Zitat</legend>', $content, $countOpened);

        $content = str_replace('[/quote]', '</fieldset>', $content, $closed);

        $content = preg_replace('/\[quote=(.*?)\]/i',
            '<fieldset class="quote"><legend>Zitat von $1</legend>',
            $content,
            -1, $countOpenedPreg
        );

        $open = $countOpened + $countOpenedPreg;

        while ($open > $closed) {
            $content .= '</fieldset>';
            $closed++;
        }

        // automatische verlinkung
        $rel = 'popup_menubar=yes,toolbar=yes,location=yes,status=yes,scrollbars=yes,resizable=yes';
        $content = preg_replace('/(http:\/\/)?(www\.[a-z0-9äöü;\/?:@=&!*~#%\'+$.,_-]+)/i', '<a href="http://$2" rel="'.$rel.'">$2</a>', $content);


        return $content;
    }
}
