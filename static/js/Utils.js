function generateOrder(productId) {
    $.ajax({
        type: "POST",
        url: "https://localhost/pay/Orders/Generate/",
        data: {
            productId: productId,
            qty : 1
        }
    }).done(function(data2) {
        window.location.href = "/pay/checkout/";
    });
}

$(document).ready(function () {

});