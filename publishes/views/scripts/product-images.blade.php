<script>
    (function($) {

        let $assetlibModalBg;
        let $assetlibModal;

        // which element is in scope; set when the asset library is launched
        let $targetInput;

        // handle incoming events from the modal > iframe
        window.document.addEventListener('assetlibEvent', function(eventData)
        {
            // handle closing the modal
            if (eventData.detail.action === 'close') {
                dismissModal();
            }

            // handle selecting an image
            if (eventData.detail.action === 'selected') {
                $targetInput.val(eventData.detail.image.fullpath);
                dismissModal();
            }

            // handle uploading an image
            if (eventData.detail.action === 'upload') {
                if (eventData.detail.error) {
                    alert(eventData.detail.error);
                } else if (eventData.detail.url) {
                    $targetInput.val(eventData.detail.url);
                } else if (eventData.detail.asset_id) {
                    $targetInput.val(eventData.detail.asset_id);
                }
                dismissModal();
            }

        }, false);

        // init each asset-picker input
        $.each($('input.asset-picker'), function(i, el) {

            let $el = $(el),
                isHiResAssetInput = $el.attr('name') === 'asset_id' || $el.attr('name') === 'rgb_asset_id';

            // write in the link to launch the browser
            if(!$el.siblings('.asset-picker-launch').size()) {
                let faClass = isHiResAssetInput ? 'fa-upload' : 'fa-picture-o';
                $('<a href="#" class="asset-picker-launch fa '+faClass+'"></a>').insertAfter($el);
            }

            let $link = $el.siblings('.asset-picker-launch');

            // pop open the asset library in a modal, for file upload/selection
            $link.unbind().on('click.assetPicker', function(e) {

                e.preventDefault();

                let iframePath = isHiResAssetInput ? '/asset-library/upload-hires' : '/asset-library/files';

                $assetlibModalBg = $('<div id="modal-bg"></div>');
                $assetlibModal = $('<div id="asset-library-modal"><iframe src="' + iframePath + '"></iframe></div>');

                $assetlibModalBg.appendTo($('body'));
                $assetlibModal.appendTo($('.wrapper'));

                $targetInput = $el;
            });
        });

        // show image previews


        let dismissModal = function() {
            $assetlibModal.fadeOut(function() {
                $assetlibModal.remove();
            });
            $assetlibModalBg.fadeOut(function() {
                $assetlibModalBg.remove();
            });
        };

    })(jQuery);
</script>