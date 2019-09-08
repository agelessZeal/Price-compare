/**
 * Created by 7Lines on 5/26/2018.
 */

var t0 = performance.now();

var products = [];
var filteredProduct = [];
var itemList = $('.product-item');
var isHighOrder = "NO_ORDER";
var fromPrice = 0;
var toPrice = Infinity;
var itemPerPage = 25;
var pageCount = Math.ceil(itemList.length / itemPerPage);
var curPage = 1;

function paginationHandler() {
    var paginationSelector = $('#pagination');
    paginationSelector.pagination({
        items: pageCount,
        displayedPages: 3,
        prevText: '<',
        nextText: '>',
        edges: 1,
        onPageClick: function (page, event) {

            window.scrollTo(0, 0);
            if (page > curPage) {
                //do something
            }
            curPage = page;
            //$('#pageloader').show();
            outputHTML();
            setTimeout(function () {
                $('#pageloader').fadeOut('slow');
            }, 500);

        }
    });
    paginationSelector.pagination('selectPage', 1);
    paginationSelector.pagination('updateItems', pageCount);
    if(filteredProduct.length<25){
        $('#pagination').hide();
    }else{
        $('#pagination').show();
    }

}

$(document).ready(function () {

    //Get products Info

    if (curPageName === 'favorite') {
        products = getLocalProducts();
        pageCount = Math.ceil(products.length / itemPerPage);
    } else {
        if(curPageName==='search' || curPageName==='profile'){

            var productsArr = pageContent.split('****');
            for(var i=0; i<productsArr.length;i=i+7){

                var tempObj = {
                    'sku': productsArr[i],
                    'title': productsArr[i+1],
                    'desc': productsArr[i+2],
                    'image': productsArr[i+6],
                    'ctg': productsArr[i+3],
                    'buy': productsArr[i+5],
                    'price': productsArr[i+4],
                    'is_fav': 'un_fav',
                    'r_pdt': ""
                };
                if (curPageName === 'profile') {
                    tempObj.is_fav = 'trash';
                }
                if(productsArr[i]){
                    products.push(tempObj);
                }
            }
        }else{
            itemList.each(function () {
                var tempObj = {
                    'sku': $(this).data('sku'),
                    'title': $(this).data('title'),
                    'desc': $(this).data('desc'),
                    'image': $(this).data('image'),
                    'ctg': $(this).data('ctg'),
                    'buy': $(this).data('link'),
                    'price': parseFloat($(this).data('price')),
                    'is_fav': $(this).data('fav'),
                    'r_pdt': ""
                };
                if (curPageName === 'profile') {
                    tempObj.is_fav = 'trash';
                }
                products.push(tempObj);
            });
        }
    }

    var t1 = performance.now();
    console.log("Call to doSomething took " + (t1 - t0) + " milliseconds.");

    paginationHandler();

    $('#higher-price-btn').click(function () {

        curPage = 1;
        $('#pageloader').show();
        if (isHighOrder!=='high'){
            products.sort(compareHigh);
        }
        paginationHandler();
        isHighOrder = 'high';

    });
    $('#lower-price-btn').click(function () {
        curPage = 1;
        $('#pageloader').show();
        if (isHighOrder!=='low'){
            products.sort(compareLow);
        }
        isHighOrder = 'low';
        paginationHandler();
    });
    $('#price-range-btn').click(function () {

        $('#price-range-drop-btn').trigger('click');

        curPage = 1;
        $('#pageloader').show();
        fromPrice = parseFloat($("input[name='range-from']").val());
        toPrice = parseFloat($("input[name='range-to']").val());
        if (isNaN(toPrice)) toPrice = Infinity;
        if (isNaN(fromPrice)) {
            fromPrice = 0;
            $("input[name='range-from']").val('0');
        }
        //outputHTML();

        paginationHandler();

    });




});
function compareLow(a, b) {
    if (Number(a.price) < Number(b.price))
        return -1;

    if (Number(a.price) > Number(b.price))
        return 1;
    return 0;
}
function compareHigh(a, b) {

    console.log(b.price);
    if (Number(a.price) < Number(b.price))
        return 1;

    if (Number(a.price) > Number(b.price))
        return -1;
    return 0;
}

/***
 * Make Product Page
 */
function outputHTML() {

    var len = products.length;
    var itemCnt = 0;

    filteredProduct  =[];

    var buttonHTMLFav = '<div class="product-compare-btns">';
    buttonHTMLFav += '<a class="btn btn-default" onclick="compareProduct(this)"><span class="glyphicon glyphicon-heart fav-active">';
    buttonHTMLFav += '</span></a></div>';
    var buttonHTMLUnFav = '<div class="product-compare-btns">';
    buttonHTMLUnFav += '<a class="btn btn-default" onclick="compareProduct(this)"><span class="glyphicon glyphicon-heart fav-inactive">';
    buttonHTMLUnFav += '</span></a></div>';

    var buttonHTMLFavoriteTrash = '<div class="product-compare-btns">';
    buttonHTMLFavoriteTrash += '<a class="btn btn-default" onclick="deleteFavoriteProduct(this)"><span class="glyphicon glyphicon-trash">';
    buttonHTMLFavoriteTrash += '</span></a></div>';

    var buttonHTMLProfileTrash = '<div class="product-compare-btns">';
    buttonHTMLProfileTrash += '<a class="btn btn-default" onclick="deleteProfileProduct(this)"><span class="glyphicon glyphicon-trash">';
    buttonHTMLProfileTrash += '</span></a></div>';

    if (toPrice > fromPrice) {
        $('.search-products-section .row').html('');
        if (len > 0) {
            for (var i = 0; i < len; i++) {
                if (products[i].price > fromPrice && products[i].price < toPrice) {
                    itemCnt++;
                    if (((curPage - 1) * itemPerPage) < itemCnt && itemCnt <= (curPage * itemPerPage)) {

                        var pdtHTML = '<div class="col-2x-grid col-sm-4 col-xs-6">';
                        pdtHTML += '<div class="product-item card" ';

                        pdtHTML += 'data-title="' + products[i].title + '" ';
                        pdtHTML += 'data-image="' + products[i].image + '" ';
                        pdtHTML += 'data-price="' + Number(products[i].price).toFixed(2) + '" ';
                        pdtHTML += 'data-desc=' + "\"" + products[i].desc.replaceAll('"','&quot;') + "\" ";
                        pdtHTML += 'data-category="' + products[i].ctg + '" ';
                        pdtHTML += 'data-link="' + products[i].buy + '" ';
                        pdtHTML += 'data-sku="' + products[i].sku + '" >';

                        if (curPageName === 'favorite' || curPageName === 'profile') {
                            if (products[i].image.indexOf('http') !== -1) {
                                pdtHTML += '<img src="' + products[i].image + '" onclick="openViewDetail(' + "'" + products[i].sku + "'" + ')">';
                            } else {
                                pdtHTML += '<img src="' + siteURL + products[i].image + '" onclick="openViewDetail(' + "'" + products[i].sku + "'" + ')">';
                            }

                        } else {

                            if (products[i].image.indexOf('http') !== -1) {
                                pdtHTML += '<img src="' + products[i].image + '" onclick="openProductView(this)">';

                            } else {
                                pdtHTML += '<img src="' + siteURL + products[i].image + '" onclick="openProductView(this)">';
                            }

                        }

                        pdtHTML += '<div class="product-item-name">' + products[i].title + '</div>';
                        pdtHTML += '<p><span>' + Number(products[i].price).toFixed(2) + '</span> USD</p>';

                        switch (products[i].is_fav) {
                            case 'fav':
                                pdtHTML += buttonHTMLFav;
                                break;
                            case 'un_fav':
                                pdtHTML += buttonHTMLUnFav;
                                break;
                            case 'trash':
                                if(curPageName==='favorite'){
                                    pdtHTML += buttonHTMLFavoriteTrash;
                                }else{
                                    pdtHTML += buttonHTMLProfileTrash;
                                }
                                break;
                        }
                        pdtHTML += '</div></div>';

                        $('.search-products-section .row').append(pdtHTML);
                    }
                    filteredProduct.push(products[i]);// we will add filtered products
                }
            }
            if (itemCnt === 0) {
                $('.search-products-section .row').html('<h3 class="no-product-fond">No product found</h3>');
            }
        } else {
            $('.search-products-section .row').html('<h3 class="no-product-fond">No product found</h3>');
        }
    } else {
        alert('To Value should be greater than From Value');
    }
    pageCount = Math.ceil(itemCnt / itemPerPage);

    setTimeout(function () {
        $('#pageloader').fadeOut('slow');
    }, 500);
}

function getRelatedProducts(pdtSku) {

    var pdtArray = [];
    var keyArr = getRandomKeyArray(0, filteredProduct.length - 1, 6);
    for (var i = 0; i < keyArr.length; i++) {
        var curSku = filteredProduct[keyArr[i]].sku;
        if (pdtSku !== curSku) {
            var pdtStr = extractValueStringFromtObject(filteredProduct[keyArr[i]], "*****");
            pdtArray.push(pdtStr);
            if (pdtArray.length > 4) break;
        }
    }
    return pdtArray;
}