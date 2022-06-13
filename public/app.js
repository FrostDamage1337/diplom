$(document).ready(function() {
    $('.btn-play').on('click', function(e) {
        let target = $(e.target).hasClass('box-price') ? $(e.target).closest('.btn-play') : $(e.target);
        $.ajax({
            url: $(target).attr('data-url'),
            method: 'POST',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                box_id: $(target).attr('data-id')
            }
        }).done(function(response) {
            if (response.alert) {
                alert(response.alert.message);
                return;
            }
            $('.modal').addClass('active');
            let offset = Math.floor(Math.random() * 10) + 1;
            offset = offset > 8 ? 8 : offset;
            let element = $('.roulette .img-wrapper[data-id="' + response.id + '"][data-offset="' + offset +'"]');
            console.log(offset);
            console.log(response.id);
            $('.modal-content').scrollLeft(0);
            $('.modal-content').animate({scrollLeft: $(element).offset().left - $('.roulette .img-wrapper[data-id="1"][data-offset="0"]').offset().left - 283}, 4000);
            setTimeout(() => {
                $('.modal').removeClass('active');
                $.ajax({
                    url: $('.balance').attr('data-url'),
                    method: 'GET',
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                    }
                }).done(function(balance) {
                    $('.balance').text(balance + '₴');
                });
                
                $('.won-item-container').addClass('active');
                $('.won-item-container .img-wrapper').css('background-image', "url('/storage/" + response.filepath + "')");
                $('.won-item-container .title').text(response.name);
                $('.won-item-container .price').text(response.price + '₴');
                $('.won-item-container .selling-price').text(response.price * 1.10 + '₴');
                let href = $('.won-item-container .actions a').attr('href');
                console.log(href.replace(/(sell\/.)/, 'sell/' + response.user_item_id));
                $('.won-item-container .actions a').attr('href', href.replace(/(sell.*)/, 'sell/' + response.user_item_id));
            }, 4400);
        });
    });
});