(function () {
  "use strict";
    tinymce.create('tinymce.plugins.dvskills', {
        init : function(ed, url) {
            ed.addButton('dvskills', {
                title : 'Add Skills',
                icon: 'icon dashicons-welcome-learn-more',
                onclick : function() {
                     ed.selection.setContent("[dvskills]<br/>[dvskill title='HTML' percent='90'][/dvskill]<br/>[dvskill title='CSS' percent='80'][/dvskill]<br/>[dvskill title='PHP' percent='75'][/dvskill]<br/>[/dvskills]");

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('dvskills', tinymce.plugins.dvskills);
})();

(function () {
  "use strict";
    tinymce.create('tinymce.plugins.dvcv', {
        init : function(ed, url) {
            ed.addButton('dvcv', {
                title : 'Add a CV Box',
                icon: 'icon dashicons dashicons-awards',
                onclick : function() {
                     ed.selection.setContent("[dvcv title='Title' subtitle='Subtitle'][/dvcv]");

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('dvcv', tinymce.plugins.dvcv);
})();