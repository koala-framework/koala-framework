<?php
class Vpc_Form_Decorator_Label extends Vpc_Form_Decorator_Abstract
{
    public function processItem($item, $errors)
    {
        if (isset($item['items'])) {
            foreach ($item['items'] as $k=>$i) {
                $item['items'][$k] = $this->processItem($i, $errors);
            }
        }
        // Kein else und auch bei preHtml - Cards hat zB. items und preHtml, hier sollte das Label aber auch funktionieren
        if (isset($item['html']) || isset($item['preHtml']) || isset($item['postHtml'])) {
            $hasErrors = false;
            if ($item['item']) {
                foreach ($errors as $e) {
                    if (isset($e['field']) && $e['field'] === $item['item']) {
                        $hasErrors = true;
                    }
                }
            }
            $class = 'vpsField';
            if ($item['item'] && $item['item']->getLabelAlign()) {
                $class .= ' vpsFieldLabelAlign'.ucfirst($item['item']->getLabelAlign());
            }
            if ($hasErrors) {
                $class .= ' vpsFieldError';
            }
            if ($item['item'] && $item['item']->getAllowBlank()===false) {
                $class .= ' vpsFieldRequired';
            }
            if ($item['item']) {
                $c = get_class($item['item']);
                $classParts = array();
                while ($c) {
                    $i = $c;
                    if (substr($i, -10) == '_Component') $i = substr($i, 0, -10);
                    $i = str_replace('_', '', $i);
                    $classParts[] = ' '.strtolower(substr($i, 0, 1)).substr($i, 1);
                    $c = get_parent_class($c);
                    if ($c == 'Vps_Form_Field_Abstract' || $c == 'Vps_Form_Field_SimpleAbstract') break;
                }
                $class .= implode(' ', array_reverse($classParts));
				if (!isset($item['id'])) {
                    $class .= ' '.$item['item']->getFieldName();
                }
            }
            if (isset($item['id'])) {
                $class .= ' '.$item['id'];
            }
            $preHtml = '<div class="'.$class.'">';
            if ($item['item'] && !$item['item']->getHideLabel() && $item['item']->getFieldLabel()) {
                $preHtml .= '<label for="'
                    .(isset($item['id']) ? $item['id'] : $item['item']->getFieldName()).'"'
                    .($item['item']->getLabelWidth() ? ' style="width:'.$item['item']->getLabelWidth().'px"' : '')
                .'>';
                $preHtml .= $item['item']->getFieldLabel();
                if ($item['item']->getAllowBlank()===false) {
                    /* TODO: wenn wir hier einmal andere Zeichen benötigen oder an einer anderen
                     * Position, dann machen wir eine reine CSS-Lösung:
                     * für alles ausser <IE8 .foo:after { content: '*'; }
                     * und für IE ein Javascript das das emuliert. Das Javascript muss Serverseitig
                     * das CSS parsen und daraus generieren welche Zeichen an welcher Stelle
                     * eingefügt werden müssen.
                     */
                    $preHtml .= '<span class="requiredSign">*</span>';
                }
                $preHtml .= $item['item']->getLabelSeparator();
                $preHtml .= '</label>';
            }
            $postHtml = '';
            if ($item['item'] && $item['item']->getComment()) {
                $postHtml .= '<span class="comment">'.$item['item']->getComment().'</span>';
            }
            $postHtml .= '</div>';
            if (!isset($item['preHtml'])) $item['preHtml'] = '';
            if (!isset($item['postHtml'])) $item['postHtml'] = '';
            $item['preHtml'] = $preHtml . $item['preHtml'];
            $item['postHtml'] = $item['postHtml'] . $postHtml;
        }
        return $item;
    }
}
