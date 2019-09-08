<?php
namespace Vanguard\Support\CJ;
/**
 * Created by PhpStorm.
 * User: 7Lines
 * Date: 5/26/2018
 * Time: 12:51 AM
 */
class CJSDK
{

    const apiBaseURL = "https://product-search.api.cj.com/v2/product-search?";
    const developerKey = "00cebf5484d8db146d0dfca22c70d86531aec526884ff1ce56d457594baadfd148832c42174a4700383c020fb9c013e4a66ec1996e7e0942ee907362d1f725fde9/00a1c5740cbee22d896778562d0fdf42cf2d4176751298d22b7edb1a33eaf056804d4443da52ecfc2eef1227a3df98bb7b991fc54ebc3a4d4e5d64af5faaeb4d01";

    const websiteID = "8815294";
    const advertiserIDS = "";   // CIDs, joined, notjoined, Empty String
    const serviceableArea = "";  //Limits the results to a specific set of advertisers’ targeted areas.
    #const isbn = "" ;            //Limits the results to a specific product from multiple merchants identified by the appropriate unique identifier; ISBN.
    #const upc = "";              //Limits the results to a specific product from multiple merchants identified by the appropriate unique identifier; UPC.
    #const manufacturerName = ""; //Limits the results to a particular manufacturer's name.
    #const manufacturerSku = "";  //Limits the results to a particular manufacturer's SKU number.
    #const advertiserSku = "";    //Limits the results to a particular advertiser SKU.
    #const lowSalePrice = "";     //Limits the results to products with a price greater than or equal to the Advertiser offered 'low-sale-price'.
    #const highSalePrice = "";    //Limits the results to products with a price less than or equal to the Advertiser offered 'high-sale-price'.
    const currency = "USD";      //Limits the results to one of the CJ supported sales tracking currencies
    const sortBy = "name";       //Sort the results in the response by one of the following values.(Name,Advertiser ID, Advertiser Name,Currency,Price,	salePrice,Manufacturer,	SKU, UPC)
    const sortOrder = "dec";     //Specifies the order in which the results are sorted; the following case-insensitive values are acceptable.(•	asc: ascending (default value),•	dec: descending)
    const pageNumber = 0;        //Specifies the first record to return in the request. The first record is 0. Leaving this parameter blank assigns a default value of 0. Please refer to the notes below the table for more information.
    const recordsPerPage = 200;  //Specifies the number of records to return in the request. Leaving this parameter blank assigns a default value of 50.
    const maxResult = 200;


    public function __construct()
    {

        //Sweaters & Knits >  V Neck


    }

    public function connectCJ($keyword, $sortBy = "asc",$lowSalePrice = null, $highSalePrice = null)
    {
        $apiURL = self::apiBaseURL;
        $apiURL .= 'website-id='.self::websiteID;
        $apiURL .= '&advertiser-ids='.self::advertiserIDS;
        //$apiURL .= '&serviceable-area='.self::serviceableArea;
        $apiURL .= '&keywords='.rawurlencode($keyword);
//        $apiURL .= '&keywords='.($keyword);
        $apiURL .= '&currency='.self::currency;
        $apiURL .= '&records-per-page='.self::recordsPerPage;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: '.self::developerKey
        ));
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiURL,
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;

    }

    public function getSubCategory()
    {
        $apiURL = 'https://support-services.api.cj.com/v2/categories?';
        $apiURL .= 'locale=en';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: '.self::developerKey
        ));
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiURL,
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;
    }

    public function getLinkTypes()
    {
        $apiURL = 'https://support-services.api.cj.com/v2/link-types?';
        $apiURL .= 'locale=en';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: '.self::developerKey
        ));
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiURL,
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;
    }

    public function getLinkSizes()
    {
        $apiURL = 'https://support-services.api.cj.com/v2/link-sizes?';
        $apiURL .= 'locale=en';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: '.self::developerKey
        ));
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiURL,
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;
    }

    public function getCurrency()
    {

        $apiURL = ' https://support-services.api.cj.com/v2/countries?';
        $apiURL .= 'locale=en';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: '.self::developerKey
        ));
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $apiURL,
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;
    }
}