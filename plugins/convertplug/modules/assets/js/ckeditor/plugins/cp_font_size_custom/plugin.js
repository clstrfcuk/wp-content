CKEDITOR.plugins.add('cp_font_size_custom', {
        requires: ['richcombo'],
        init: function(editor) {
            var pluginName = 'cp_font_size_custom';
            var config = editor.config,
                lang = editor.lang.format;

            editor.ui.addRichCombo('photogalleries', {
                label: "Фоторепортаж",
                title: "Фоторепортаж",
                voiceLabel: "Фоторепортаж",
                className: 'cke_format',
                multiSelect: false,
                icon: CKEDITOR.plugins.getPath('cp_font_size_custom') + 'photo-list-horizontal.png',

                panel: {
                    css: [config.contentsCss, CKEDITOR.getUrl(editor.skinPath + 'editor.css')],
                    voiceLabel: lang.panelVoiceLabel
                },

                init: function () {
                    this.startGroup("Фоторепортаж");
                    var list=this;
                    $("#_photogalleries option:selected").each(function(index, value){
                        console.log(index, value);
                        list.add("#HORIZONTAL_GALLERY_"+ $(value).val()+"#", "(Г) " + $(value).text(), "(Г) " + $(value).text());
                        list.add("#VERTICAL_GALLERY_"+ $(value).val()+"#",   "(В) " + $(value).text(), "(В) " + $(value).text());
                    });
                },

                onClick: function (value) {
                    editor.focus();
                    editor.fire('saveSnapshot');
                    editor.insertHtml(value);
                    editor.fire('saveSnapshot');
                }
            });
        }
});