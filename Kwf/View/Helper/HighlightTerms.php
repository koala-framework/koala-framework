<?php
class Kwf_View_Helper_HighlightTerms
{
    /**
     * Highlights a term in a text and shorts it. The output then may look like
     * the description of a web-search-engine result.
     *
     * @param string|array $terms The term(s) to highlight. Each must contain at least a char of: a-z A-Z 0-9
     * @param string $text The text to highlight the terms in.
     * @param array $options Options for the highlighting.
     *     Possible keys an their default values are:
     *
     *     maxReturnLength => 350 // the maximum text-length to be returned. Set to '0' to return full string.
     *     maxReturnBlocks => 3 // maximum blocks to be returned
     *     blockSeparator => ' ... ' // string with which blocks are separated
     *     cutWithinWords => false // if the blocks are cut within words or only on a space
     * @return string $highlightenedText The highlightened and shortened text.
     */
    public function highlightTerms($terms, $text, array $options = array())
    {
        if (!is_array($terms)) $terms = array($terms);

        if (!isset($options['maxReturnLength'])) $options['maxReturnLength'] = 350;
        if (!isset($options['maxReturnBlocks'])) $options['maxReturnBlocks'] = 3;
        if (!isset($options['blockSeparator'])) $options['blockSeparator'] = ' ... ';
        if (!isset($options['cutWithinWords'])) $options['cutWithinWords'] = false;

        foreach ($terms as $k => $term) {
            if (!preg_match('/[a-zA-Z0-9]/', $term)) {
                unset($terms[$k]);
            }
        }
        $terms = array_values($terms);

        if ($options['maxReturnLength'] == 0) {
            $ret = $text;
        } else {
            // get from / to block positions
            $blocksPositions = array();
            foreach ($terms as $term) {
                $term = preg_quote($term, "/");
                preg_match_all("/(^|\W)($term)(\W|$)/i", $text, $matches, PREG_OFFSET_CAPTURE);
                $m = $matches[2];
                $blocks = count($m) > $options['maxReturnBlocks'] ? $options['maxReturnBlocks'] : count($m);
                if ($blocks >= 1) {
                    $blockLength = $options['maxReturnLength'];
                    $blockLength /= $blocks;
                    $blockLength -= $blocks * mb_strlen($options['blockSeparator']);
                    $blockLength = floor($blockLength);
                    for ($i=0; $i<$blocks; $i++) {
                        $blockPos = floor($m[$i][1] - (($blockLength - mb_strlen($term)) / 2));
                        if ($blockPos < 0) $blockPos = 0;
                        $blocksPositions[$blockPos] = array(
                            'pos' => $blockPos,
                            'length' => $blockLength
                        );
                    }
                }
            }

            // sorting the blocks if there was more than one search word
            ksort($blocksPositions);
            $blocksPositions = array_values($blocksPositions);

            // block begin / end only on space character
            if (!$options['cutWithinWords'] && $blocksPositions) {
                foreach ($blocksPositions as $k => $blockPos) {
                    // check start
                    if ($blockPos['pos'] != 0 ) {
                        while (preg_match('/\S/', mb_substr($text, $blockPos['pos']-1, 1)) // davor kein whitespace
                            || preg_match('/\s/', mb_substr($text, $blockPos['pos'], 1)) // beginn ein whitespace
                        ) {
                            $blockPos['pos'] += 1;
                        }
                        $blocksPositions[$k] = $blockPos;
                    }

                    // check end
                    if ($blockPos['pos'] + $blockPos['length'] < mb_strlen($text)) {
                        while (preg_match('/\S/', mb_substr($text, $blockPos['pos']+$blockPos['length'], 1)) // danach kein whitespace
                            || preg_match('/\s/', mb_substr($text, $blockPos['pos']+$blockPos['length']-1, 1)) // ende ein whitespace
                        ) {
                            $blockPos['length'] -= 1;
                        }
                        $blocksPositions[$k] = $blockPos;
                    }
                }
            }

            // merge overlapping blocks
            if ($blocksPositions) {
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
            }

            // building the new string
            if (!$blocksPositions) {
                $ret = mb_substr($text, 0, $options['maxReturnLength']);
            } else {
                $ret = '';
                $i = 1;
                foreach ($blocksPositions as $blockPos) {
                    $retBlock = mb_substr($text, $blockPos['pos'], $blockPos['length']);
                    if ($i > $options['maxReturnBlocks']
                        || mb_strlen($ret) + mb_strlen($retBlock) > $options['maxReturnLength']
                    ) {
                        // wenn die zeichen die zuviel sind, weniger als 30%
                        // des blocks ausmachen, vorn und hinten wegschneiden
                        $signsTooMuch = (mb_strlen($ret) + mb_strlen($retBlock)) - $options['maxReturnLength'];
                        if ($signsTooMuch <= mb_strlen($retBlock) * 0.3) {
                            $retBlock = mb_substr($retBlock, ceil($signsTooMuch/2), (-1)*ceil($signsTooMuch/2));
                            if ($blockPos['pos'] != 0) $ret .= $options['blockSeparator'];
                            $ret .= $retBlock;
                            $lastBlockPos = $blockPos;
                        }

                        // breaken, ab hier wirds wirklich zu lang
                        break;
                    }
                    if ($blockPos['pos'] != 0) $ret .= $options['blockSeparator'];
                    $ret .= $retBlock;
                    $lastBlockPos = $blockPos;
                    $i++;
                }
                if (!empty($lastBlockPos)) {
                    if ($lastBlockPos['pos'] + $lastBlockPos['length'] < mb_strlen($text)) {
                        $ret .= $options['blockSeparator'];
                    }
                }
            }
        }

        // highlighting
        $c = 1;
        foreach ($terms as $term) {
            $term = preg_quote($term, "/");
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
