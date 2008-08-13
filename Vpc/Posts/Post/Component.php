<?php
class Vpc_Posts_Post_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['edit'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Post_Edit_Component',
            'name' => trlVps('edit')
        );
        $ret['generators']['report'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Post_Report_Component',
            'name' => trlVps('report')
        );
        $ret['generators']['delete'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Post_Delete_Component',
            'name' => trlVps('delete')
        );
        $ret['generators']['quote'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Post_Quote_Component',
            'name' => trlVps('quote')
        );
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $data = $this->getData();
        
        $ret['content'] = self::replaceCodes($data->row->content);
        $ret['delete'] = false;
        if ($this->mayEditPost()) {
            $ret['edit'] = $data->getChildComponent('_edit');
            $ret['delete'] = $data->getChildComponent('_delete');
        }
        $ret['report'] = $data->getChildComponent('_report');
        $ret['quote'] = $data->getChildComponent('_quote');
        $ret['user'] = $data->parent->getComponent()->getUserComponent($data->row->user_id);
        $ret['signature'] = null;
        if ($ret['user']) {
            $ret['signature'] = nl2br(htmlspecialchars($ret['user']->row->signature));
        }
        $select = $data->parent->getGenerator('detail')->select($data->parent)
            ->where('create_time <= ?', $data->row->create_time)
            ->where('id != ?', $data->row->id)
            ->order(array('create_time', 'id'));
        $ret['postNumber'] = count($select->query(Zend_Db::FETCH_NUM)->fetchAll()) + 1;
        return $ret;
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
