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
                if (substr($c, -10) == '_Component') $c = substr($c, 0, -10);
                $c = str_replace('_', '', $c);
                $class .= ' '.strtolower(substr($c, 0, 1)).substr($c, 1);
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
