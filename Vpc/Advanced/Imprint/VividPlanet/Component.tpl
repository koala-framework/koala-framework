<div class="<?=$this->cssClass?>">
    <span class="imprintHeadline"><? if (!$this->row->hide_webdesign) { echo trlVps('Webdesign')." / "; } echo trlVps('Programming');?></span>
    <p>
        <a href="http://www.vivid-planet.com/" title="Internet-Agentur Salzburg" rel="popup_blank"><img height="20" alt="Internet-Agentur Salzburg Logo" src="/assets/vps/Vpc/Advanced/Imprint/VividPlanet/vividplanet.png" width="87" /></a><br/>
        <?=trlVps('Vivid Planet Software GmbH');?><br/>
        <a href="http://www.vivid-planet.com/" rel="popup_blank" title="Internet-Agentur Salzburg"><?=trlVps('Internet-Agentur Salzburg');?></a><br/>
        <?=trlVps('Pfongauerstraße 67');?><br/>
        <?=trlVps('A-5202 Neumarkt a. Wallersee');?><br/>
        <p><a href="http://www.vivid-planet.com/" title="Internet-Agentur Salzburg" rel="popup_blank"><?=trlVps('http://www.vivid-planet.com');?></a><br/>
        <? if ($this->row->is_isiweb) { ?>
            <?=trlVps('This {0} is an {1}',array('<a href="http://www.vivid-planet.com/leistungen/isiweb_websitepaket" rel="popup_blank">'.trlVps("Homepage").'</a>','<a href="http://www.vivid-planet.com/leistungen/isiweb_websitepaket" rel="popup_blank">'.trlVps("isiWEB").'</a>'))?>
            (<a href="http://www.isiweb.at" rel="popup_blank"><?=trlVps('www.isiweb.at');?></a>).<br/>
            <?=trlVps('A product of Vivid Planet Software GmbH');?>.
        <? } ?>
    </p>
</div>

