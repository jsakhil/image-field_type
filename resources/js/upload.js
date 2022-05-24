// Disabling autoDiscover, otherwise Dropzone will try to attach twice.
Dropzone.autoDiscover = false;

$(function () {

    var uploaded = [];

    var uploader = $('#upload');
    var element = $('.dropzone');
    var template = uploader.find('.template');
    var preview = template.html();

    template.remove();

    var dropzone = new Dropzone('.dropzone:not(data-initialized)',
        {
            paramName: 'upload',
            url: '/streams/image-field_type/handle',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            init: function () {
                $('.dropzone').attr('data-initialized', '');
            },
            sending: function (file, xhr, formData) {
                formData.append('folder', element.data('folder'));
            },
            accept: function (file, done) {
                $.getJSON('/admin/files/exists/' + element.data('folder') + '/' + file.name, function (data) {
                    if (data.exists) {
                        if (!confirm(file.name + " " + element.data('overwrite'))) {
                            dropzone.removeFile(file);
                            return;
                        }
                    }

                    done();
                });
            },
            autoQueue: true,
            thumbnailWidth: 24,
            thumbnailHeight: 24,
            previewTemplate: preview,
            previewsContainer: '.uploads',
            maxFilesize: element.data('max-size'),
            acceptedFiles: element.data('allowed'),
            parallelUploads: element.data('max-parallel'),
            dictDefaultMessage: element.data('icon') + ' ' + element.data('message'),
            uploadprogress: function (file, progress) {
                file.previewElement.querySelector("[data-dz-uploadprogress]").setAttribute('value', progress);
            }
        }
    );

    // While file is in transit.
    dropzone.on('sending', function () {
        uploader.find('.uploaded .card-block').html(element.data('uploading') + '...');
    });

    // When file successfully uploads.
    dropzone.on('success', function (file) {

        var response = JSON.parse(file.xhr.response);

        uploaded.push(response.id);

        file.previewElement.querySelector('[data-dz-uploadprogress]').setAttribute('class', 'progress progress-success');

        setTimeout(function () {
            file.previewElement.remove();
        }, 500);
    });

    // When file fails to upload.
    dropzone.on('error', function (file) {
        file.previewElement.querySelector("[data-dz-uploadprogress]").setAttribute('value', 100);
        file.previewElement.querySelector('[data-dz-uploadprogress]').setAttribute('class', 'progress progress-danger');
    });

    // When all files are processed.
    dropzone.on('queuecomplete', function () {

        uploader.find('.uploaded .modal-body').html(element.data('loading') + '...');

        uploader.find('.uploaded').load('/streams/image-field_type/recent?uploaded=' + uploaded.join(','));
    });
});
