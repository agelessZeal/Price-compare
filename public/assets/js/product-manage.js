var lobibox;

function continueChilling() {
    lobibox.hide();
}
function openYourFavorites() {
    window.location.href = favroitePageURL;
}

function showFavoriteConfirm() {

    lobibox = Lobibox.window({
        content:'<h3 style="text-align: center">Item has been added to favorites!</h3>' +
        '<div class="confirm-btns-container">' +
        '<button class="btn btn-danger input-lg" id="open-your-favorites-btn" onclick="openYourFavorites()">Open your favorites</button>' +
        '<button class="btn btn-danger input-lg" id="continue-chilling-btn" onclick="continueChilling()">Continue chilling</button>' +
        '</div>',
        height:200,
        width:440,
        closeButton:false
    });
}
function compareProduct(self) {

    var pdtItem  = self.parentNode.parentNode;

    var pdtTitle = $(pdtItem).data('title');
    var pdtImage = $(pdtItem).data('image');
    var pdtCategory = $(pdtItem).data('category');
    var pdtDescription = $(pdtItem).data('desc');
    var pdtPrice = $(pdtItem).data('price');
    var pdtLink = $(pdtItem).data('link');
    var pdtSku = $(pdtItem).data('sku');

    var relatedProduct = getRelatedProducts(pdtSku);

    if(curPageName!=='favorite' || curPageName!=='profile'){ ///We will use local storage to store favorites items
        $(self).find('span').removeClass('fav-inactive');
        $(self).find('span').addClass('fav-active');

        addToLocalStorage(pdtTitle, pdtImage, pdtCategory,
                        pdtDescription, pdtPrice, pdtLink, pdtSku);
        $.ajax({
            type:'post',
            url:ajaxStoreRelatedProduct,
            data:{
                _token:tokenStr,
                title:pdtTitle,
                image:pdtImage,
                category:pdtCategory,
                description:pdtDescription,
                link:pdtLink,
                price:pdtPrice,
                sku:pdtSku,
                related_products:relatedProduct
            },
            beforeSend:function (xhr) {}
        }).done(function (res) {
            if(res.status=='success'){
                console.log(res.data);
                console.log('We saved this information into related_product page too.. in search page');
            }else{
                console.log('Compare Failed');
            }
        }).fail(function (res) {
            console.log('Server Error')
        }).always(function (jqXHR, textStatus) {
            if (textStatus !== "success") {
                console.log(jqXHR);
            } else {
                console.log(jqXHR);
            }
        })
    }
    showFavoriteConfirm();
}

/***
 * We will use this function in favorite page and profile page
 * @param self
 */
function compareViewProduct(self)
{
    var pdtItem  = self.parentNode.parentNode;
    var pdtTitle = $(pdtItem).data('title');
    var pdtImage = $(pdtItem).data('image');
    var pdtCategory = $(pdtItem).data('category');
    var pdtDescription = $(pdtItem).data('desc');
    var pdtPrice = $(pdtItem).data('price');
    var pdtLink = $(pdtItem).data('link');
    var pdtSku = $(pdtItem).data('sku');

    $(self).find('span').removeClass('fav-inactive');
    $(self).find('span').addClass('fav-active');

    addToLocalStorage(pdtTitle, pdtImage, pdtCategory,
        pdtDescription, pdtPrice, pdtLink, pdtSku);
    showFavoriteConfirm();
}

function openProduct(self) {
    $(self.parentNode.parentNode.parentNode).submit();
}

function openProductView(self) {

    var relatedProduct = getRelatedProducts();

    var itemTag = self.parentNode;
    var pdtTitle = $(itemTag).data('title');
    var pdtImage = $(itemTag).data('image');
    var pdtCategory = $(itemTag).data('category');
    var pdtDescription = $(itemTag).data('desc');
    var pdtPrice = $(itemTag).data('price');
    var pdtLink = $(itemTag).data('link');
    var pdtSku = $(itemTag).data('sku');

    var responseSKU = "";

    setTimeout(function () {
        if(responseSKU!==""){
            openViewDetail(responseSKU);
        }else {
            alert('server error');
        }

    },800);

    $.ajax({
        type:'post',
        url:ajaxStoreRelatedProduct,
        data:{
            _token:tokenStr,
            title:pdtTitle,
            image:pdtImage,
            category:pdtCategory,
            description:pdtDescription,
            link:pdtLink,
            price:pdtPrice,
            sku:pdtSku,
            related_products:relatedProduct
        },
        async: false,
        beforeSend:function (xhr) {}
    }).done(function (res) {
        if(res.status=='success'){
            console.log(res.data);
            responseSKU = res.data;
        }else{
            console.log('Compare Failed');
        }
    }).fail(function (res) {
        console.log('Server Error')
    }).always(function (jqXHR, textStatus) {
        if (textStatus !== "success") {
            console.log(jqXHR);
        } else {
            console.log(jqXHR);
        }
    });

}
function deleteFavoriteProduct(self) {

    var pdtItem  = self.parentNode.parentNode;
    var pdtSku = $(pdtItem).data('sku');
    deleteLocalProduct(pdtSku);
    window.location.reload();
}

function deleteProfileProduct(self) {

    var pdtItem  = self.parentNode.parentNode;
    var pdtSku = $(pdtItem).data('sku');
    $.ajax({
        type:'post',
        url: ajaxProfileProductDeleteURL,
        data:{_token:tokenStr,sku:pdtSku},
        beforeSend:function (xhr) {}
    }).done(function (res) {
        if(res.status=='success'){
            //alert('Product has been added to your profile!');
            window.location.reload();
        }else{
            alert('Compare Failed');
        }
    }).fail(function (res) {
        alert('Server Error')
    }).always(function (jqXHR, textStatus) {
        if (textStatus !== "success") {
            console.log(jqXHR);
        } else {
            console.log(jqXHR);
        }
    })
}