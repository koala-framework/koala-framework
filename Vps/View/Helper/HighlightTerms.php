<?php
class Vps_View_Helper_HighlightTerms
{
    /**
     * Highlights a term in a text and shorts it. The output then may look like
     * the description of a web-search-engine result.
     *
     * @param string|array $terms The term(s) to highlight.
     * @param string $text The text to highlight the terms in.
     * @param array $options Options for the highlighting.
     *     Possible keys an their default values are:
     *
     *     maxReturnLength => 200 // the maximum text-length to be returned. Set to '0' to return full string.
     *     maxReturnBlocks => 3 // maximum blocks to be returned
     *     blockSeparator => ' ... ' // string with which blocks are separated
     * @return string $highlightenedText The highlightened and shortened text.
     */
    public function highlightTerms($terms, $text, array $options = array())
    {
        if (!is_array($terms)) $terms = array($terms);

        if (!isset($options['maxReturnLength'])) $options['maxReturnLength'] = 200;
        if (!isset($options['maxReturnBlocks'])) $options['maxReturnBlocks'] = 3;
        if (!isset($options['blockSeparator'])) $options['blockSeparator'] = ' ... ';

        if ($options['maxReturnLength'] == 0) {
            $ret = $text;
        } else {
            // get from / to block positions
            $blocksPositions = array();
            foreach ($terms as $term) {
                preg_match_all("/(^|\W)($term)(\W|$)/i", $text, $matches, PREG_OFFSET_CAPTURE);
                $m = $matches[2];
                $blocks = count($m) > $options['maxReturnBlocks'] ? $options['maxReturnBlocks'] : count($m);
                $blockLength = $options['maxReturnLength'];
                $blockLength -= $blocks * mb_strlen($options['blockSeparator']);
                $blockLength /= $blocks;
                $blockLength = floor($blockLength);
                for ($i=0; $i<$blocks; $i++) {
                    $blockPos = floor($m[$i][1] - (($blockLength - mb_strlen($term)) / 2));
                    if ($blockPos < 0) $blockPos = 0;
                    $blocksPositions[] = array(
                        'pos' => $blockPos,
                        'length' => $blockLength
                    );
                }
            }

            // merge overlapping blocks
            do {
                $merged = false;
                for ($i=0; $i<count($blocksPositions); $i++) {
                    $curPos = $blocksPositions[$i];
                    $nextPos = isset($blocksPositions[$i+1]) ? $blocksPositions[$i+1] : null;
                    if (!$nextPos) break;

                    if ($curPos['pos'] + $curPos['length'] >= $nextPos['pos']) {
                        $blocksPositions[$i]['length'] = ($nextPos['pos'] + $nextPos['length']) - $curPos['pos'];
                        unset($blocksPositions[$i+1]);
                        // assigning new keys
                        $blocksPositions = array_values($blocksPositions);
                        $merged = true;
                        break;
                    }
                }
            } while ($merged);

            // building the new string
            $ret = '';
            foreach ($blocksPositions as $blockPos) {
                if ($blockPos['pos'] != 0) $ret .= $options['blockSeparator'];
                $ret .= mb_substr($text, $blockPos['pos'], $blockPos['length']);
                $lastBlockPos = $blockPos;
            }
            if ($lastBlockPos) {
                if ($lastBlockPos['pos'] + $lastBlockPos['length'] < mb_strlen($text)) {
                    $ret .= $options['blockSeparator'];
                }
            }
        }

        // highlighting
        $c = 1;
        foreach ($terms as $term) {
            $ret = preg_replace(
                "/(^|\W)($term)(\W|$)/i",
                '$1<span class="highlightTerms highlightTerm'.$c.'">$2</span>$3',
                $ret
            );
            $c++;
        }

        return $ret;
    }
}
