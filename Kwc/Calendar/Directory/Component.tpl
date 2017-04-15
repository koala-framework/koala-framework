<div class="<?=$this->cssClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <h1>Unsere Termine</h1>
    <p>
    Um detailierte Informationen zu erhalten,<br/> klicken Sie auf einen violett hinterlegten Termin.
    </p>
    <h2><?=$this->currentMonth?></h2>
    <?=$this->componentLink($this->data, 'zurück', array('get' => array('date' => $this->back)))?> - 
    <?=$this->componentLink($this->data, 'weiter', array('get' => array('date' => $this->next)))?>
    <div class="days">
        <?
            $i=1;
            foreach ($this->days as $day) {
                $event = '';
                if ($day['event']) {
                    $eventText = $day['event']->row->title;
                    if (strlen($eventText) > 7) {
                        $eventText = substr($eventText, 0, 6).'.';
                    }
                    $event = "<div class=\"eventText\">".
                        $eventText.
                        "</div>";
                }
                $dayLink = $day['dayNumber'];
                echo "<div class=\"day {$day['class']}\">".
                    "<div class=\"dayNumber\">".
                        $day['dayNumber'].
                    "</div>".
                    $event.
                "</div>";
                $i++;
            }
            echo '<div class="clear"></div>';
        ?>
    </div>
</div>
