<?php

namespace App\Http\Controllers;

use ZfrShopify\OAuth\AuthorizationRedirectResponse;

use GuzzleHttp\Client;
use ZfrShopify\OAuth\TokenExchanger;

class ShopifyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function index()
    {

        $_db = 'stores.json';
        if(!empty($_REQUEST)){
        
            # Guard
            isset($_GET['shop']) or die ('Query parameter "shop" missing.');
            preg_match('/^[a-zA-Z0-9\-]+.myshopify.com$/', $_GET['shop']) or die('Invalid myshopify.com store URL.');

            // $install_url = shopify\install_url($_GET['shop'], SHOPIFY_APP_API_KEY);

            $shopDomain = $_GET['shop'];

            $scopes         = ['read_orders', 'read_products'];
            $redirectUri = APP_BASE_URL.'/oauth';
            $nonce          = 'strong_nonce';


            // $response = new AuthorizationRedirectResponse( API_KEY, $shopDomain, $scopes, $redirectUri, $nonce );

            $uri = sprintf(
                'https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s&state=%s',
                str_replace('.myshopify.com', '', $shopDomain),
                API_KEY,
                implode(',', $scopes),
                $redirectUri,
                $nonce
            );

            header("location:".$uri);
            die();


        }
    }


    public function oauth()
    {

        # Guard
        isset($_GET['shop']) or die ('Query parameter "shop" missing.');
        preg_match('/^[a-zA-Z0-9\-]+.myshopify.com$/', $_GET['shop']) or die('Invalid myshopify.com store URL.');

        $shop = trim($_GET['shop']);
        $code = trim($_GET['code']);
        $hmac = trim($_GET['hmac']);
        $state = trim($_GET['state']);
        $timestamp = trim($_GET['timestamp']);

        $store = str_replace('.myshopify.com', '', $shop);

        //Check if Store already exists in our DB
        if(file_exists(STORES_DB)) $shopifyStores = (array) json_decode(file_get_contents( STORES_DB ));
        else $shopifyStores = array();

        //pr( $shopifyStores );

        $shopifyStores[$store]->store = $store;
        $shopifyStores[$store]->code = $code;
        $shopifyStores[$store]->hmac = $hmac;
        $shopifyStores[$store]->shop = $shop;
        $shopifyStores[$store]->state = $state;
        $shopifyStores[$store]->timestamp = $timestamp;


        //Write the DB updates
        write_file( STORES_DB, json_encode($shopifyStores) );
        // pr( $shopifyStores, 1 );

        $storeRedirectURI = "https://".$shop."/admin/apps";
        header("location:".$storeRedirectURI);
        die();

    }


    public function customizer()
    {


   
        $shop = trim($_GET['shop']);
        $code = trim($_GET['signature']);
       
        $apiKey         = API_KEY;
        $sharedSecret   = APP_SECRET;
        $shopDomain     = "https://".$shop."/admin/apps";
        $code           = $code;

        $tokenExchanger = new TokenExchanger(new Client());
        //pr($tokenExchanger);
        $accessToken    = $tokenExchanger->exchangeCodeForToken($apiKey, $sharedSecret, $shopDomain, $code);
        pr($accessToken,1);
         header('Content-Type:application/liquid');

         //echo "hello";   
    }


    //
}
