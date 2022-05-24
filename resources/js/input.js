$(document).on('ajaxComplete ready', function () {

    // Initialize file pickers
    $('[data-provides="anomaly.field_type.image"]:not([data-initialized])').each(function () {

        $(this).attr('data-initialized', '');

        var input = $(this);
        var field = input.data('field_name');
        var modal = $('#' + field + '-modal');
        var wrapper = input.closest('.form-group');
        var image = wrapper.find('[data-provides="cropper"]');

        var options = {
            viewMode: 2,
            zoomable: false,
            autoCropArea: 1,
            responsive: false,
            checkOrientation: false,
            data: image.data('data'),
            aspectRatio: image.data('aspect-ratio'),
            minContainerHeight: image.data('min-container-height'),
            build: function () {
                if (image.attr('src').length) {
                    image.closest('.cropper').removeClass('hidden');
                }
            },
            crop: function (e) {

                /**
                 * This prevents trashy data from
                 * being parsed into the field value.
                 */
                if (!isFinite(e.x) || isNaN(e.x) || typeof e.x == 'undefined' || e.x == null) {
                    return;
                }

                $('[name="' + field + '[data]"]').val(JSON.stringify({
                    'x': e.x,
                    'y': e.y,
                    'width': e.width,
                    'height': e.height,
                    'rotate': e.rotate,
                    'scaleX': e.scaleX,
                    'scaleY': e.scaleY
                }));
            }
        };

        if (image.closest('.tab-content').length) {
            options.minContainerWidth = image.closest('.tab-content').width();
        }

        if (image.closest('.field-group.image').length) {
            options.minContainerWidth = image.closest('.card-block').width() - 32;
        }

        if (image.closest('.grid-body').length) {
            options.minContainerWidth = image.closest('.grid-item').width() - 32;
        }

        if (image.closest('.repeater-body').length) {
            options.minContainerWidth = image.closest('.repeater-item').width() - 32;
        }


        image.cropper(options);

        modal.on('click', '[data-file]', function (e) {

            e.preventDefault();

            modal.find('.modal-content').append('<div class="modal-loading"><div class="active loader"></div></div>');

            wrapper.find('.selected').load('/streams/image-field_type/selected?uploaded=' + $(this).data('file'), function () {
                modal.modal('hide');
            });

            image.closest('.cropper').removeClass('hidden');

            image
                .cropper('replace', '/streams/image-field_type/view/' + $(this).data('file'))
                .cropper('reset');

            $('[name="' + field + '[id]"]').val($(this).data('file'));
        });

        $(wrapper).on('click', '[data-dismiss="file"]', function (e) {

            e.preventDefault();

            $('[name="' + field + '[id]"]').val('');
            $('[name="' + field + '[data]"]').val('');

            wrapper.find('.selected').load('/streams/image-field_type/selected', function () {

                modal.modal('hide');

                image.closest('.cropper').addClass('hidden');
            });
        });
    });
});
