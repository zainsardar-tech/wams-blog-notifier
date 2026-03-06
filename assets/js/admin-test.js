jQuery(document).ready(function ($) {
    $('#wams-test-connection').on('click', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $result = $('#wams-test-result');

        // Disable button and show loading state
        $button.prop('disabled', true).text('Testing...');
        $result.removeClass('success error').html('<span class="spinner is-active" style="float:none;"></span> Testing connection...');

        // Send AJAX request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wams_test_connection',
                nonce: wams_admin_obj.nonce
            },
            success: function (response) {
                if (response.success) {
                    $result.addClass('success').html('✅ ' + response.data.message);
                } else {
                    $result.addClass('error').html('❌ ' + response.data.message);
                }
            },
            error: function (xhr, status, error) {
                $result.addClass('error').html('❌ Connection test failed: ' + error);
            },
            complete: function () {
                // Re-enable button
                $button.prop('disabled', false).text('Test Connection');
            }
        });
    });
});
