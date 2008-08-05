<?php
class Vpc_Posts_Post_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['edit'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Post_Edit_Component',
            'name' => trlVps('Edit')
        );
        $ret['generators']['report'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Post_Report_Component',
            'name' => trlVps('Report')
        );
        $ret['tablename'] = 'Vpc_Posts_Model';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        
        $ret['content'] = self::replaceCodes($this->getData()->row->content);
        $ret['edit'] = false;
        if ($this->mayEditPost()) {
            $ret['edit'] = $this->getData()->getChildComponent('-edit');
        }
        $ret['report'] = $this->getData()->getChildComponent('-report');

        $ret['user'] = $this->getData()->row->findParentRow(Vps_Registry::get('userModel'));
        return $ret;
    }

    public function setPostNum($postNum)
    {
        //TODO
        $this->_postNum = $postNum;
    }

    public function mayEditPost()
    {
        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
        if (!$authedUser) return false;

        return $authedUser->id == $this->getData()->row->user_id;
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
        
        return nl2br($content);
    }
}
