jQuery(document).ready(function ($) {
    try {
        // Media uploader
        $('#jolt_custom_image_button').on('click', function (e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Select or Upload Custom Loader Image',
                button: { text: 'Use this image' },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#jolt_custom_image').val(attachment.url);
                $('#jolt_custom_image').trigger('change');
            });

            frame.open();
        });
    } catch (error) {
        console.error('Error in admin.js:', error);
    }
});