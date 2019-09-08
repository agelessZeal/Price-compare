/*
$('.category-part').hover(
    function () {
        $('.category-back-drop').show();
        $('.category-section-container').show();
    },
    function () {
        $('.category-back-drop').hide();
        $('.category-section-container').hide();
    }
);
*/
$('#view-all-category').click(function () {
    $('.category-back-drop').toggleClass('custom-show');
    $('.category-section-container').toggleClass('custom-show');
});

// $('.category-item-title').click(function () {
//
//     var keyword = ($(this).data('keyword'));
//     $('input[name="category"]').val(keyword);
//     $('#product-search-form').submit();
// });

$('#search-all-category').change(function () {
    $('#products-search-form').submit();
});

$('.category-sub-item').click(function () {
    var keyword = $(this).data('keyword');
    $('input[name="category"]').val(keyword);
    $('#product-search-form').submit();
    //location.href = productSearchURL + "?category="+keyword+"&keyword=";
});

$('#price-range-drop-btn').click(function () {
    $(this).toggleClass('price-range-btn-select');
    $('#range-search-form').toggle();
});

$('.featherlight-close-icon').click(function () {
    $('.featherlight').hide();
});

$('.featherlight').click(function (e) {
    var tgtClass = $(e.target).attr('class');
    if(tgtClass==='featherlight')
        $(this).hide();
});

//Remove All Local Products
$('#fav-remove-all-btn').click(function () {
    removeAllLocalProduct();
    window.location.reload();
});

document.addEventListener('click',function (e) {

    var str = e.target.className;
    console.log(str);
    if(str.indexOf("custom-show")!==-1||str.indexOf('category-section')!==-1){
        $('.category-back-drop').toggleClass('custom-show');
        $('.category-section-container').toggleClass('custom-show');
    }

});

