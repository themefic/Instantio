(function($) {
    'use strict';

    jQuery(document).ready(function() {

        //Add To Cart Fly Effect
        $(document).on('click', '.add_to_cart_button:not(.product_type_variable), .single_add_to_cart_button:not(.disabled)', function() {

            $('body').append('<div id="wi-cart-fly">'+instantio_ajax_params.cart_icon+'</div>');

            var endPos = $("#wi-toggler").offset();
            var startPos = $(this).offset();

            $('#wi-cart-fly').css({
                'top': startPos.top + 'px',
                'left': startPos.left + 'px'
            })
            .animate({
                opacity: 1,
                top: endPos.top,
                left: endPos.left
            }, 1500, function() {
                $(this).css({
                    'opacity': '0',
                    'z-index': '0'
                });
                $(this).detach();
            });
        });

        // Added to cart JS
        // For custom trigger use https://pastebin.com/Y2QpTm1b
        $(document.body).on('added_to_cart', function() {
            setTimeout(function(){
                if ( wiCartTotal > 0 ) {
                    $('#wi-toggler').addClass( 'hascart' );
                } else {
                    $('#wi-toggler').removeClass( 'hascart' );
                }
            },300);
        });

    });

})(jQuery);

// Toggle Panel
(function($) {
    'use strict';

    jQuery(document).ready(function() {

        //Nav Toggler
        $(document).on('click', '#wi-toggler, .added_to_cart', function(e) {
            //e.preventDefault();

            var targetClass = $('#wi-toggler, .added_to_cart');

            if (targetClass.hasClass('open')) {
                targetClass.removeClass('open');
                $('.wi-container').removeClass('panel-open');
                $('html').removeClass('wi-panel-open');
            } else {
                targetClass.addClass('open');
                $('.wi-container').addClass('panel-open');
                $('html').addClass('wi-panel-open');
            }
            //Update cart on Nav Toggle
            jQuery('[name="update_cart"]').trigger('click'); // Update Cart

        });

        //Collapse Nav if click on body
        $('html').on('click', function (e) {
            if (!$('#wi-toggler, .added_to_cart, .add_to_cart_button').is(e.target) && $('#wi-toggler, .added_to_cart, .add_to_cart_button').has(e.target).length === 0 && !$('.wi-inner').is(e.target) && $('.wi-inner').has(e.target).length === 0) {
                $('#wi-toggler, .added_to_cart').removeClass('open');
                $('.wi-container').removeClass('panel-open');
                $('html').removeClass('wi-panel-open');
            }
        });

    });

})(jQuery);

// For quick view
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

// Single Product Ajax Cart
(function($) {
    'use strict';
    jQuery(document).ready(function() {

        $(document).on('click', '.single_add_to_cart_button:not(.disabled)', function (e) {
            e.preventDefault();

            var thisbutton = $(this),
                cart_form = thisbutton.closest('form.cart'),
                id = thisbutton.val(),
                product_qty = cart_form.find('input[name=quantity]').val() || 1,
                product_id = cart_form.find('input[name=product_id]').val() || id,
                variation_id = cart_form.find('input[name=variation_id]').val() || 0;

            var data = {
                action: 'wi_single_ajax_add_to_cart',
                product_id: product_id,
                product_sku: '',
                quantity: product_qty,
                variation_id: variation_id,
            };

            $(document.body).trigger('adding_to_cart', [thisbutton, data]);

            $.ajax({
                type: 'post',
                url: instantio_ajax_params.wi_ajax_url,
                data: data,
                beforeSend: function (response) {
                    thisbutton.removeClass('added').addClass('loading');
                },
                complete: function (response) {
                    thisbutton.addClass('added').removeClass('loading');
                },
                success: function (response) {
                    if (response.error & response.product_url) {
                        window.location = response.product_url;
                        return;
                    } else {
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, thisbutton]);
                    }
                },
            });

            return false;
        });

    });
})(jQuery);