$(document).on('submit', '#search-panel form', function() {
    var $form       = jQuery(this);
    var $input      = $form.find('input[name="s"]');
    var query       = $input.val();
    var checkBox    = false;
    if ($('input:checkbox').is(':checked')) { checkBox = true; }
    var $content    = jQuery('#content')

    jQuery.ajax({
        type    : 'post',
        url     : myAjax.ajaxurl,
        data    : {
            action  : 'kb_search',
            query   : query,
            checkBox: checkBox
        },
        beforeSend: function() {
            $input.prop('disabled', true);
            $content.addClass('loading');
        },
        success:    function( response ) {
            $input.prop('disabled', false);
            $content.removeClass('loading');
            $content.html( response );
        }
    });

    return false;
})