var local_pdt_key = 'bp_products';
function addToLocalStorage(pdtTitle, pdtImage, pdtCategory, pdtDescription, pdtPrice, pdtLink, pdtSku)
{
  var localProducts = getLocalProducts();
  var newProduct = {
          title:pdtTitle,
          image:pdtImage,
          ctg:pdtCategory,
          desc:pdtDescription,
          price:pdtPrice,
          buy: pdtLink,
          sku:pdtSku,
          is_fav :'trash'
    };
  if(localProducts.length>0 && !isExist(pdtSku,localProducts)){
      console.log(localProducts.length);
      localProducts.push(newProduct);
      localStorage.setItem(local_pdt_key,JSON.stringify(localProducts));

  }else{
      var jsPdtArr = [newProduct];
      localStorage.setItem(local_pdt_key,JSON.stringify(jsPdtArr));
  }
}
function getLocalProducts() {

    var localProductsJOSN = localStorage.getItem(local_pdt_key);
    if(localProductsJOSN){
        return JSON.parse(localProductsJOSN);
    }else{
        return [];
    }

}
function isExist(sku, products) { //Duplication of product

    for (var i = 0 ;i<products.length; i++){
        if(products[i].sku===sku) return true;
    }
    return false;

}
function deleteLocalProduct(sku) { //Delete of product by sku

    var localProducts = getLocalProducts();
    for (var i = 0 ;i<localProducts.length; i++){
        if(localProducts[i].sku===sku){
            localProducts.splice(i,1);
            localStorage.setItem(local_pdt_key,JSON.stringify(localProducts));
            break;
        }
    }
}
function removeAllLocalProduct() {
    localStorage.setItem(local_pdt_key,'');
}