<? if ($this->data->hasContent()) { ?>
    <div class="<?=$this->cssClass?>">

        <div id="flashMediaPlayer<?= $this->row->component_id; ?>">
            <a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.
        </div>

        <script type="text/javascript">
            var s1 = new SWFObject("<?= $this->playerPath; ?>","ply<?= $this->row->component_id; ?>","<?= $this->row->width; ?>","<?= $this->row->height; ?>","9","#FFFFFF");
            s1.addParam("allowfullscreen","true");
            s1.addParam("allowscriptaccess","always");
            s1.addParam("flashvars","file=<?= $this->url; ?>"
                <? if ($this->row->autostart) { ?>
                    +"&autostart=true"
                <? } ?>
                <? if ($this->row->loop) { ?>
                    +"&repeat=always"
                <? } ?>
            );
            s1.write("flashMediaPlayer<?= $this->row->component_id; ?>");
        </script>
    </div>
<? } ?>