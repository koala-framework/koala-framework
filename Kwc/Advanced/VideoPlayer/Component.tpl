<div class="<?=$this->cssClass?>">
    <video width="320" height="240" poster="poster.jpg" controls="controls" preload="none">
        <!-- MP4 for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7 -->
        <source type="video/mp4" src="myvideo.mp4" />
        <!-- WebM/VP8 for Firefox4, Opera, and Chrome -->
        <source type="video/webm" src="myvideo.webm" />
        <!-- Ogg/Vorbis for older Firefox and Opera versions -->
        <source type="video/ogg" src="myvideo.ogv" />
        <!-- Optional: Add subtitles for each language -->
        <track kind="subtitles" src="subtitles.srt" srclang="en" />
        <!-- Optional: Add chapters -->
        <track kind="chapters" src="chapters.srt" srclang="en" />
        <!-- Flash fallback for non-HTML5 browsers without JavaScript -->
        <object width="320" height="240" type="application/x-shockwave-flash" data="flashmediaelement.swf">
            <param name="movie" value="flashmediaelement.swf" />
            <param name="flashvars" value="controls=true&file=myvideo.mp4" />
            <!-- Image as a last resort -->
            <img src="myvideo.jpg" width="320" height="240" title="No video playback capabilities" />
        </object>
    </video>
</div>
