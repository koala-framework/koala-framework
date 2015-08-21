var responsiveEl = require('kwf/responsive-el');

responsiveEl('.kwcClass.default', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 500, cls: 'gt500'}, {minWidth: 350, cls: 'gt350'}]);
responsiveEl('.kwcClass.centerDefault', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 500, cls: 'gt500'}, {minWidth: 350, cls: 'gt350'}]);
responsiveEl('.kwcClass.smallBox', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 350, cls: 'gt350'}]);
responsiveEl('.kwcClass.center', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 350, cls: 'gt350'}]);
