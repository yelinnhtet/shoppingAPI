<?php

use App\Http\Controllers\Api\AdsController;
use App\Models\Currency;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\CityApiController;
use App\Http\Controllers\Api\GoodApiController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\WithdrawController;
use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\GoodRatingController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\RatingUserController;
use App\Http\Controllers\Api\ShopRatingController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\Good_paraApiController;
use App\Http\Controllers\Api\Good_specApiController;
use App\Http\Controllers\Api\ShippingAddressController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PromotionApiController;
use App\Http\Controllers\Api\PromotionItemApiController;
use App\Models\Ads;

Route::post('user/register',[UserController::class,'register']);
Route::post('user/updateUser',[UserController::class,'update']);
Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::get('userList', [UserController::class,'index']);
Route::apiResource('country', CountryApiController::class);
Route::apiResource('state', StateController::class);
Route::post('state/getStateByID', [StateController::class, 'getStateByID']);
Route::apiResource('city', CityApiController::class);
Route::post('city/getCityByID', [CityApiController::class, 'getCityByID']);
Route::apiResource('currency', CurrencyController::class);
Route::apiResource('good', GoodApiController::class);
Route::post('good/{good}', [GoodApiController::class,'update']);
Route::get('goodSearch/find', [GoodApiController::class,'searchGoodName']);

Route::apiResource('good_spec', Good_specApiController::class);
Route::post('good_spec/{good_spec}', [Good_specApiController::class,'update']);
Route::apiResource('good_para', Good_paraApiController::class);
Route::post('good_para/{good_para}', [Good_paraApiController::class,'update']);
Route::get('user', [UserController::class,'index']);
Route::post('user/userList', [UserController::class,'getUserList']);
Route::post('user/changeStatus', [UserController::class,'changeStatus']);
Route::get('countryCode', [CountryApiController::class,'getCountryCode']);
Route::post('user/remove', [UserController::class,'remove']);
Route::post('user/uploadPhoto', [UserController::class, 'uploadPhoto']);
Route::post('shop', [ShopController::class,'index']);
Route::post('shop/shopList', [ShopController::class,'getShopList']);
Route::post('shop/logo', [ShopController::class, 'logo']);
Route::post('shop/coverPhoto', [ShopController::class, 'coverPhoto']);
Route::post('shop/shopkeeperNrc', [ShopController::class, 'shopkeeperNrc']);
Route::post('shop/saveShop', [ShopController::class, 'store']);
Route::get('shop/getShop', [ShopController::class, 'getShop']);
// Route::post('shop/updateShop',[ShopController::class, 'update']);
Route::post('orderByMonth', [OrderController::class,'getOrderByMonth']);
Route::get('getOrder', [OrderController::class,'index']);
Route::post('order/orderList', [OrderController::class,'getOrderList']);
Route::post('product/productList', [GoodApiController::class, 'getProductList']);
Route::post('product/saveProduct', [GoodApiController::class, 'saveProduct']);
Route::post('product/updateProduct', [GoodApiController::class, 'updateProduct']);
Route::post('product/removeProduct', [GoodApiController::class, 'removeProduct']);
Route::post('product/uploadImage', [GoodApiController::class, 'uploadImage']);
Route::post('ads/adsList', [AdsController::class, 'getAdsList']);
Route::post('ads/uploadImage', [AdsController::class, 'uploadImage']);
Route::post('ads/saveAds', [AdsController::class, 'store']);
Route::post('ads/updateAds', [AdsController::class, 'update']);
Route::post('ads/remove', [AdsController::class, 'remove']);
Route::post('promotion/promotionsList', [PromotionApiController::class, 'getPromotionsList']);
Route::post('promotion/savePromotions', [PromotionApiController::class, 'store']);
Route::post('promotion/updatePromotions', [PromotionApiController::class, 'update']);
Route::post('promotion/removePromotions', [PromotionApiController::class, 'remove']);
Route::post('promotion/uploadImage', [PromotionApiController::class, 'uploadImage']);
Route::post('category/categoryList', [CategoryApiController::class, 'getCategoryList']);
Route::post('category/saveCategory', [CategoryApiController::class, 'saveCategory']);
Route::post('category/updateCategory', [CategoryApiController::class, 'updateCategory']);
Route::post('category/removeCategory', [CategoryApiController::class, 'removeCategory']);
Route::post('category/uploadImage', [CategoryApiController::class, 'uploadImage']);
Route::get('category/getCategories', [CategoryApiController::class, 'getCategories']);
Route::post('currency/currencyList', [CurrencyController::class, 'getCurrencyList']);
Route::post('currency/saveCurrency', [CurrencyController::class, 'saveCurrency']);
Route::post('currency/updateCurrency', [CurrencyController::class, 'updateCurrency']);
Route::post('currency/removeCurrency', [CurrencyController::class, 'removeCurrency']);
Route::post('currency/uploadImage', [CurrencyController::class, 'uploadImage']);
Route::get('getCurrencies', [CurrencyController::class, 'getCurrencies']);

Route::apiResource('shop', ShopController::class);
Route::get('/shopLimit', [ShopController::class,'shopLimit']);

/* Route::group(['middleware' => 'auth:api'],function(){

    Route::post('shop/{shop}',[ShopController::class,'update']);

    Route::apiResource('payment', PaymentApiController::class);
    Route::post('payment/{payment}', [PaymentApiController::class,'update']);

    Route::apiResource('shopRating', ShopRatingController::class);
    Route::post('shopRating/{shopRating}', [ShopRatingController::class,'update']);

    Route::apiResource('goodRating', GoodRatingController::class);
    Route::post('goodRating/{goodRating}', [GoodRatingController::class,'update']);

    Route::apiResource('category', CategoryApiController::class);
    Route::post('category/{category}', [CategoryApiController::class,'update']);

    Route::apiResource('order',OrderController::class);
    Route::post('order/{order}',[OrderController::class,'update']);

    Route::apiResource('wallet', WalletController::class);
    Route::post('wallet/{wallet}', [WalletController::class,'update']);

    Route::apiResource('delivery', DeliveryController::class);
    Route::post('delivery/{delivery}', [DeliveryController::class,'update']);

    Route::apiResource('withdraw', WithdrawController::class);
    Route::post('withdraw/{withdraw}', [WithdrawController::class,'update']);

    Route::apiResource('shippingadress', ShippingAddressController::class);
    Route::post('shippingadress/{shippingadress}', [ShippingAddressController::class,'update']);
    Route::get('userShippingAddress/{user_id}',[ShippingAddressController::class,'shippingAddressByUserId']);

    Route::post('country/{country}', [CountryApiController::class,'update']);

    Route::post('currency/{currency}', [CurrencyController::class,'update']);

    Route::post('state/{state}', [StateController::class,'update']);
    Route::get('searchByCountryId/{country_id}', [StateController::class,'searchByCountryId']);

    Route::post('city/{city}', [CityApiController::class,'update']);
    Route::get('searchByStateId/{state_id}', [CityApiController::class,'searchByStateId']);

    Route::post('user/{user}',[UserController::class,'update']);
    Route::delete('deleteUser/{user}',[UserController::class,'destroy']);
    Route::get('user/{user}',[UserController::class,'show']);
    Route::get('getAuthUser/{user}',[UserController::class,'getAuthUser']);

    Route::get('shopListByUserId/{id}',[ShopController::class,'shopListByUserId']);
    Route::get('orderByUserId/{user_id}',[OrderController::class,'searchOrderByUserId']);
    Route::get('searchOrderByShopId/{id}',[OrderController::class,'searchOrderByShopId']);
    Route::get('getBalanceByUserId/{id}',[WalletController::class,'getBalanceByUserId']);
    Route::get('getWithdrawHistoryByUserId/{id}',[WithdrawController::class,'getWithdrawHistoryByUserId']);
    Route::get('getWalletHistoryByUserId/{id}',[WalletController::class,'getWalletHistoryByUserId']);

    Route::apiResource('/promotion',PromotionApiController::class);
    Route::post('promotion/{promotion}', [PromotionApiController::class,'update']);

    Route::apiResource('/promotionItem',PromotionItemApiController::class);
    Route::post('promotionItem/{promotionItem}', [PromotionItemApiController::class,'update']);

    Route::get('/getGoodsByShopId/{shop_id}',[GoodApiController::class,'getGoodsByShopId']);

}); */

Route::post('/cart',[CartController::class,'cart']);
Route::post('/cart/{cart}',[CartController::class,'cartUpdate']);
Route::get('/cartPayment',[CartController::class,'index']);
Route::get('/cartListByUserId/{user_id}',[CartController::class,'searchCartByUserId']);
Route::delete('/cartDel/{id}',[CartController::class,'delete']);
Route::get('searchCategory',[CategoryApiController::class,'search']);
Route::get('searchShop',[ShopController::class,'search']);
// Route::get('forgetPassword',[ForgotPasswordController::class,'showForgetPasswordForm']);
Route::get('forgetPassword',[ForgotPasswordController::class,'searchByEmail']);
// Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm']);
Route::post('resetPassword', [ForgotPasswordController::class, 'resetPwd']);

Route::get('searchRatingByGoodId/{good_id}',[GoodRatingController::class,'searchRatingByGoodId']);
Route::get('searchRatingByGoodId',[GoodApiController::class,'searchRatingByGoodId']);
