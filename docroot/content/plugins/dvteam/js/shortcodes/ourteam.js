(function () {
  "use strict";
    tinymce.create('tinymce.plugins.dvteam', {
        init : function(ed, url) {
            ed.addButton('dvteam', {
                title : 'Add DV-Team',
                icon : 'icon dashicons-groups',
                onclick : function() {
                     ed.selection.setContent("[dvteam max='99' categoryid='' gridstyle='full' offset='20' itemwidth='250' side='right']");

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('dvteam', tinymce.plugins.dvteam);
})();

(function () {
  "use strict";
    tinymce.create('tinymce.plugins.dvteamfilter', {
        init : function(ed, url) {
            ed.addButton('dvteamfilter', {
                title : 'Add Filterable DV-Team',
                icon : 'icon dashicons-images-alt',
                onclick : function() {
                     ed.selection.setContent("[dvteamfilter max='99' gridstyle='full' offset='20' itemwidth='250' side='right' exclude='']");

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('dvteamfilter', tinymce.plugins.dvteamfilter);
})();

(function () {
  "use strict";
    tinymce.create('tinymce.plugins.dvteamcarousel', {
        init : function(ed, url) {
            ed.addButton('dvteamcarousel', {
                title : 'Add DVTeam-Carousel',
                icon : 'icon dashicons-image-flip-horizontal',
                onclick : function() {
                     ed.selection.setContent("[dvteamcarousel max='99' categoryid='' columns='3' gridstyle='square' autoplay='false' duration='4' spacing='20' side='right']");

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('dvteamcarousel', tinymce.plugins.dvteamcarousel);
})();

(function () {
  "use strict";
    tinymce.create('tinymce.plugins.dvthumbnails', {
        init : function(ed, url) {
            ed.addButton('dvthumbnails', {
                title : 'Add DVTeam-Thumbnails',
                icon : 'icon dashicons-screenoptions',
                onclick : function() {
                     ed.selection.setContent("[dvthumbnails max='99' categoryid='' side='right']");

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('dvthumbnails', tinymce.plugins.dvthumbnails);
})();