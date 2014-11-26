Kwf.onJElementReady('.kwcSocialMedia2ClickButtons', function(el, config) {
    el.children('.socialShareButtons').socialSharePrivacy({
        services: {
            facebook: {
                'status': (config.showFacebook) ? 'on' : 'off',
                'txt_info': config.services.facebook.txtInfo,
                'txt_fb_off': config.services.facebook.txtFbOff,
                'txt_fb_on': config.services.facebook.txtFbOn,
                'dummy_caption': config.services.facebook.dummyCaption,
                'language': config.services.facebook.language
            },
            twitter: {
                'status': (config.showTwitter) ? 'on' : 'off',
                'txt_info': config.services.twitter.txtInfo,
                'txt_twitter_off': config.services.twitter.txtTwitterOff,
                'txt_twitter_on': config.services.twitter.txtTwitterOn,
                'dummy_caption': config.services.twitter.dummyCaption,
                'language': config.services.twitter.language
            },
            gplus: {
                'status': (config.showGoogle) ? 'on' : 'off',
                'txt_info': config.services.gplus.txtInfo,
                'txt_gplus_off': config.services.gplus.txtGPlusOff,
                'txt_gplus_on': config.services.gplus.txtGPlusOn,
                'language': config.services.gplus.language
            }
        },
        'txt_help': config.txtHelp,
        'settings_perma': config.settingsPerma,
        'settings': config.settings,
        'css_path': ''
    });
});


