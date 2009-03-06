<div class="<?=$this->cssClass?>">
    <span class="imprintHeadline"><? if (!$this->row->hide_webdesign) { echo trlVps('Webdesign')." / "; } echo trlVps('Programming');?></span>
    <p>
        <a href="http://www.vivid-planet.com/" title="Internet-Agentur Salzburg" rel="popup_blank"><img height="20" alt="Internet-Agentur Salzburg Logo" src="/assets/vps/Vpc/Advanced/Imprint/VividPlanet/vividplanet.gif" width="87" /></a><br/>
        Vivid Planet Software GmbH<br/>
        <a href="http://www.vivid-planet.at/" rel="popup_blank" title="Internet-Agentur Salzburg">Internet-Agentur Salzburg</a><br/>
        Pfongauerstraße 67<br/>
        A-5202 Neumarkt a. Wallersee<br/>
        <p><a href="http://www.vivid-planet.at/" title="Internet-Agentur Salzburg" rel="popup_blank">http://www.vivid-planet.at</a><br/>
        <? if ($this->row->is_isiweb) { ?>
            Diese <a href="http://www.vivid-planet.at/produkte/isiweb_homepage.html" rel="popup_blank">Homepage</a>
            ist ein <a href="http://www.vivid-planet.at/produkte/isiweb_homepage.html" rel="popup_blank">isiWEB</a>
            (<a href="http://www.isiweb.at" rel="popup_blank">www.isiweb.at</a>).<br/>
            Ein Produkt der Vivid Planet Software GmbH.
        <? } ?>
    </p>
</div>

