jQuery( function( $ ){
    $(document).ready(function(){

        // Add Quick View Panel DIV to body
        $(document.body).append('<div class="wi-quick-view"></div>');

        // Close Quick View Panel
        $(document).on('click', '.wi-quick-view .close', function(e) {
            $(this).parent().fadeOut( 300 );
        })

        // Variable Product Quick View Ajax on Click
        $(document).on('click', '.product_type_variable', function(e) {
            e.preventDefault();

            var $this = $(this),
                cartPos = $this.offset(),
                product_id = $this.data('product_id');

            $('.wi-quick-view').css({
                'top': parseInt( cartPos.top ) + parseInt( 45 ) + 'px',
                'left': cartPos.left + 'px'
            })

            $.ajax({
                type: 'post',
                url: instantio_ajax_params.wi_ajax_url,
                data: {
                    action: 'wi_variable_product_quick_view',
                    security: instantio_ajax_params.wi_ajax_nonce,
                    product_id: product_id,
                },
                beforeSend: function(data){
                    $this.addClass('loading');
                    $('.wi-quick-view').block();
                },
                success: function(data){
                    $this.removeClass('loading');
                    $('.wi-quick-view').fadeIn( 300 ).html(data).prepend('<span class="close"></span>');
                },
                error: function(data){
                    console.log(data);
                },
            });
        });

    });
});
