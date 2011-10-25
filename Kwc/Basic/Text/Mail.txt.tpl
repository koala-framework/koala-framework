<?php
$lastLinkComponent = null;
foreach ($this->contentParts as $part) {
    if (!is_string($part) && isset($part['type']) && $part['type'] == 'link') {
        $lastLinkComponent = $part;
    } else if (is_string($part)) {
        if ($lastLinkComponent && strpos($part, '</a>') !== false) {
            $part = str_replace('</a>', ': '.$this->component($lastLinkComponent['component']), $part);
            $lastLinkComponent = null;
        }
        echo strip_tags($part);
    } else {
        echo $this->component($part['component']);
    }
}
?>