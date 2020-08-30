tinymce.PluginManager.add('lana_download', function (editor) {

    editor.addButton('lana_download', {
        tooltip: 'Download Shortcode',
        icon: 'lana-download',
        cmd: 'lanaDownloadShortcodeCmd'
    });

    editor.addCommand('lanaDownloadShortcodeCmd', function () {

        jQuery.post(ajaxurl, {
            action: 'lana_downloads_manager_get_lana_download_list'
        }, function (response) {

            /** error */
            if (false === response['success']) {
                alert(response['data']['message']);
            }

            /** success */
            if (true === response['success']) {
                var lanaDownloadList = response['data']['lana_download_list'],
                    lanaDownloadValues = [{
                        'text': 'Select File...',
                        'value': '',
                        'disabled': true,
                        'selected': true,
                        'hidden': true
                    }];

                /** add list to values */
                jQuery.each(lanaDownloadList, function (key, value) {
                    lanaDownloadValues.push({
                        'text': '#' + key + ' - ' + value,
                        'value': key
                    });
                });

                editor.windowManager.open({
                    title: 'Download',
                    body: [
                        {
                            type: 'listbox',
                            name: 'file',
                            label: 'File',
                            values: lanaDownloadValues,
                            minWidth: 350
                        },
                        {
                            type: 'textbox',
                            name: 'text',
                            label: 'Text',
                            minWidth: 350
                        }
                    ],
                    onsubmit: function (e) {
                        editor.focus();

                        var file = e.data.file;
                        var text = e.data.text;

                        if (null === file) {
                            return false;
                        }

                        var id_attr = '';
                        var text_attr = '';

                        if (file) {
                            id_attr = ' id="' + file + '"';
                        }

                        if (text) {
                            text_attr = ' text="' + text + '"';
                        }

                        editor.execCommand('mceInsertContent', false, '[lana_download' + id_attr + text_attr + ']');
                    }
                });
            }
        });
    });
});