<? if ($this->data->hasContent()) { ?>
    <div class="<?=$this->cssClass?>">

        <div id="flash<?= $this->row->component_id; ?>">
            <a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.
        </div>

        <script type="text/javascript">
            var s1 = new SWFObject("<?= $this->url; ?>","ply<?= $this->row->component_id; ?>","<?= $this->row->width; ?>","<?= $this->row->height; ?>","9","#FFFFFF");
            s1.addParam("quality", "high");
            s1.addParam("wmode", "opaque");
            s1.addParam("allowscriptaccess", "always");
            s1.addParam("flashVars", "<?
                if (count($this->flashVars)) {
                    $r = array();
                    foreach ($this->flashVars as $fk => $fv) {
                        $r[] = $fk.'='.urlencode($fv);
                    }
                    echo implode('&', $r);
                }
            ?>");
            s1.write("flash<?= $this->row->component_id; ?>");
        </script>
    </div>
<? } ?>
