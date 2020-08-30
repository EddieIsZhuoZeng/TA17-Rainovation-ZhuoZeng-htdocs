jQuery(function () {

    var wpMediaLanaDownloadsManagerFrame;

    jQuery('.lana-downloads-manager').find('a.upload-file-button').on('click', function (event) {

        var $lanaDownloadsManager = jQuery('.lana-downloads-manager');
        var $uploadFileButton = jQuery(this);
        var $uploadFileUrl = $lanaDownloadsManager.find('input.upload-file-url');
        var $uploadFileId = $lanaDownloadsManager.find('input[type="hidden"].upload-file-id');

        event.preventDefault();

        if (wpMediaLanaDownloadsManagerFrame) {
            wpMediaLanaDownloadsManagerFrame.close();
        }

        wpMediaLanaDownloadsManagerFrame = wp.media.frames.lanaDownloadsManager = wp.media({
            title: $uploadFileButton.data('dialog-title'),
            library: {
                type: ''
            },
            button: {
                text: $uploadFileButton.data('dialog-button')
            },
            multiple: false,
            states: [
                new wp.media.controller.Library({
                    library: wp.media.query(),
                    multiple: false,
                    title: $uploadFileButton.data('dialog-title')
                })
            ]
        });

        wpMediaLanaDownloadsManagerFrame.on('open', function () {
            var attachment = wp.media.attachment($uploadFileId.val());
            attachment.fetch();

            var selection = wpMediaLanaDownloadsManagerFrame.state().get('selection');
            selection.add(attachment ? [attachment] : []);
        });

        wpMediaLanaDownloadsManagerFrame.on('select', function () {

            var attachment = wpMediaLanaDownloadsManagerFrame.state().get('selection').first().toJSON();

            $uploadFileUrl.val(attachment.url);
            $uploadFileId.val(attachment.id);
        });

        wpMediaLanaDownloadsManagerFrame.on('ready', function () {
            wpMediaLanaDownloadsManagerFrame.uploader.options.uploader.params = {
                type: 'lana_download'
            };
        });

        wpMediaLanaDownloadsManagerFrame.open();
    });

});