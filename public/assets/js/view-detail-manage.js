
function initViewPage() {

    var localPdts = getLocalProducts();
    var isExist = false;
    for (var i = 0 ;i<localPdts.length; i++){
        if(localPdts[i].sku===cur_sku){
            isExist = true;
          break;
        }
    }
    var pdtInfoBtnHTML = '';
    var goToShopHTML = '<div class="col-sm-6 col-xs-12 go-shop-link" ><a  href="'+cur_pdt_link+'" target="_blank">'+goToShopPageStr+'</a></div></div>';
    if(isExist){

        pdtInfoBtnHTML = '<div class="row"><div class="col-sm-6 col-xs-12 add-remove-fav-btn"><button class="btn btn-danger btn-lg remove-from-fav-btn" type="button" onclick="removeFromFavorite(this)">';
        pdtInfoBtnHTML += '<span class="glyphicon glyphicon-heart fav-red"></span>&nbsp;' + removeFromFavoriteStr +'</button></div>';
        pdtInfoBtnHTML += goToShopHTML;

    }else{
        pdtInfoBtnHTML = '<div class="row"><div class="col-sm-6 col-xs-12 add-remove-fav-btn"><button class="btn btn-danger btn-lg" type="button" onclick="addToFavorite(this)">';
        pdtInfoBtnHTML += '<span class="glyphicon glyphicon-heart fav-white"></span>' + addToFavoriteStr +'</button></div>';
        pdtInfoBtnHTML += goToShopHTML;
    }

    $('.product-info-btns').html(pdtInfoBtnHTML);

}

initViewPage();

function addToFavorite(self) {

    var infoTag = self.parentNode.parentNode.parentNode.parentNode.parentNode;
    var pdtTitle =  $(infoTag).data('title');
    var pdtImage =  $(infoTag).data('image');
    var pdtCategory =  $(infoTag).data('category');
    var pdtDescription =  $(infoTag).data('desc');
    var pdtPrice =  $(infoTag).data('price');
    var pdtLink =  $(infoTag).data('link');
    var pdtSku =  $(infoTag).data('sku');

    addToLocalStorage(pdtTitle, pdtImage, pdtCategory,
        pdtDescription, pdtPrice, pdtLink, pdtSku);

    if(isLogin){
        $.ajax({
            type:'post',
            url: ajaxProductCompare,
            data:{_token:tokenStr,
                title:pdtTitle,
                image:pdtImage,
                category:pdtCategory,
                description:pdtDescription,
                link:pdtLink,
                price:pdtPrice,
                sku:pdtSku,
                related_products:""
            },
            beforeSend:function (xhr) {}
        }).done(function (res) {
            if(res.status=='success'){
                //alert('Product has been added to your profile!');
                //alert(res.data);
                console.log(res.data);
                initViewPage();
                showFavoriteConfirm();

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
    }else{
        initViewPage();
        showFavoriteConfirm();
    }

}

function removeFromFavorite(self) {

    deleteLocalProduct(cur_sku);
    initViewPage();
}

