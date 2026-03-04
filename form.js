$(function () {

    function after_form_submitted(data) {

        $('#send_message').val('Send Message');

        if (data.success === true) {
            $('#success_message').show();
            $('#error_message').hide();
            $('#contact_form')[0].reset();
            grecaptcha.reset();
        } else {
            $('#error_message').html(data.message).show();
            $('#success_message').hide();
        }
    }

    $('#contact_form').submit(function (e) {
        e.preventDefault();

        $('#send_message').val('Sending ...');

        $.ajax({
            type: "POST",
            url: "handler.php",
            data: $(this).serialize(),
            dataType: "json",
            success: after_form_submitted,
            error: function () {
                $('#send_message').val('Send Message');
                $('#error_message').html('Server error').show();
            }
        });
    });

});
