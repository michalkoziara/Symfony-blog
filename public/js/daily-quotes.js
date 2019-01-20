$(document).ready(function () {
    $.ajax({
        method: 'GET',
        dataType: "json",
        success: function (data) {
            console.log(data);
        },
        error: function (error) {
            console.log("Error:");
            console.log(error);
        },
        url: 'http://quotes.rest/qod.json'
    }).done(function (data) {
        var $quote = '"' + data.contents.quotes[0].quote + '"<br> — ' + data.contents.quotes[0].author;
        $('.js-comment').html($quote);
    })
});