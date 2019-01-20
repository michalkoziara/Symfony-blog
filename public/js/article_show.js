$(document).ready(function () {

    $('.js-like-article').on('click', function (e) {
        e.preventDefault();
        var $link = $(e.currentTarget);

        $.ajax({
            method: 'POST',
            url: $link.attr('href'),
            success: function (response) {
                if ('' !== $link.attr('href')) {
                    $link.toggleClass('fa-heart-o').toggleClass('fa-heart').attr('href', '');
                }
            },
        }).done(function (data) {
            $('.js-like-article-count').html(data.hearts);
        })
    });
});