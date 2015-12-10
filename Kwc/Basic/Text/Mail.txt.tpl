<?php
$output = '';
foreach ($this->contentParts as $part) {
    if (is_string($part)) {
        $output .= $part;
    } else {
        if ($part['type'] == 'link' || $part['type'] == 'download') {
            $output .= '<a href="' . $this->component($part['component']) . '">';
        } else {
            $output .= $this->component($part['component']);
        }
    }
}
echo Kwc_Basic_Text_HtmlToTextParser::parse($output);
