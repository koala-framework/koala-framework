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

        $postsComponent = $this->getParentComponent();
        $ret['reportUrl'] = '';
        $reportMail = $this->getSetting(get_class($postsComponent), 'reportMail');
        if ($reportMail) {
            $ret['reportUrl'] = $postsComponent->getPageFactory()
                ->getChildPageById('report')->getUrl().'?reportPost='.$row->id;
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

        // smileys
        $content = preg_replace('/:-?\)/', '<img class="emoticon_smile" src="/assets/web/images/spacer.gif" alt=":-)" border="0" />', $content);
        $content = preg_replace('/:-?D/', '<img class="emoticon_grin" src="/assets/web/images/spacer.gif" alt=":-D" border="0" />', $content);
        $content = preg_replace('/:-?P/', '<img class="emoticon_tongue" src="/assets/web/images/spacer.gif" alt=":-P" border="0" />', $content);
        $content = preg_replace('/:-?\(/', '<img class="emoticon_unhappy" src="/assets/web/images/spacer.gif" alt=":-(" border="0" />', $content);
        $content = preg_replace('/;-?\)/', '<img class="emoticon_wink" src="/assets/web/images/spacer.gif" alt=";-)" border="0" />', $content);

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
        if (!function_exists('replaceLinks')) {
            function replaceLinks($matches) {
                $rel = 'popup_menubar=yes,toolbar=yes,location=yes,status=yes,scrollbars=yes,resizable=yes';
                $showUrl = $matches[5];
                if (strlen($showUrl) > 62) {
                    $showUrl = substr($showUrl, 0, 60).'...';
                }
                return "<a href=\"http://{$matches[3]}{$matches[5]}\" title=\"{$matches[3]}{$matches[5]}\" rel=\"$rel\">{$matches[3]}$showUrl</a>";
            }
        }

        $content = preg_replace_callback(
            '/((http:\/\/)|(www\.)|(http:\/\/www\.)){1,1}([a-z0-9äöü;\/?:@=&!*~#%\'+$.,_-]+)/i',
            'replaceLinks',
            $content
        );

        return $content;
    }
}
