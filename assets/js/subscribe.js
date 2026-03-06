jQuery(document).ready(function($) {
    $('#wams-subscribe-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $msg = $('#wams-message');
        var $btn = $('#wams-submit');
        
        $msg.html('Processing...').removeClass('wams-success wams-error');
        $btn.prop('disabled', true);

        $.ajax({
            url: wams_bn_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'wams_subscribe_action',
                security: wams_bn_obj.nonce,
                phone: $('#wams-phone').val()
            },
            success: function(response) {
                if (response.success) {
                    $msg.html(response.data).addClass('wams-success');
                    $form[0].reset();
                } else {
                    $msg.html(response.data).addClass('wams-error');
                }
            },
            error: function() {
                $msg.html('An error occurred. Please try again.').addClass('wams-error');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
});
