<div id="dogearSmall"></div>
<div id="dogearBig"></div>

<script type="text/javascript">
    var s1 = new SWFObject("/assets/vps/Vpc/Box/DogearRandom/Dogear/dogear.swf","dogearSmallPlayer","180","180","9","#FFFFFF");
    s1.addParam("allowfullscreen","false");
    s1.addParam("allowscriptaccess","always");
    s1.addParam("wmode","transparent");
    s1.addParam("swLiveConnect","true");
    s1.addParam("flashvars",
        "picurl=<?= $this->urlSmall; ?>"
        +"&color1=0x<?= $this->colorRow->color_small_1; ?>&color2=0x<?= $this->colorRow->color_small_2; ?>"
        +"&linktarget=<?= ($this->linkOpen ? 'blank' : 'self'); ?>"
        +"&clicktag=<?= $this->linkUrl; ?>"
    );
    s1.write("dogearSmall");

    var s2 = new SWFObject("/assets/vps/Vpc/Box/DogearRandom/Dogear/dogear_large.swf","dogearBigPlayer","680","680","9","#FFFFFF");
    s2.addParam("allowfullscreen","false");
    s2.addParam("allowscriptaccess","always");
    s2.addParam("wmode","transparent");
    s2.addParam("swLiveConnect","true");
    s2.addParam("flashvars",
        "picurl=<?= $this->urlBig; ?>"
        +"&color1=0x<?= $this->colorRow->color_big_1; ?>&color2=0x<?= $this->colorRow->color_big_2; ?>"
        +"&linktarget=<?= ($this->linkOpen ? 'blank' : 'self'); ?>"
        +"&clicktag=<?= $this->linkUrl; ?>"
    );
    s2.write("dogearBig");
</script>