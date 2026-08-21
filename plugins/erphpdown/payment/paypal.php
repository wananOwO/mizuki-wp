<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
session_start();
if(isset($_GET['redirect_url'])){
    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
}else{
    $_COOKIE['erphpdown_return'] = '';
    setcookie('erphpdown_return','',0,'/');
}
header("Content-type:text/html;character=utf-8");
require_once('../../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');

require_once('paypal/CallerService.php');
require_once("paypal/APIError.php");

$ice_payapl_api_rmb = get_option('ice_payapl_api_rmb')?get_option('ice_payapl_api_rmb'):1;//汇率

if(!isset($_REQUEST['token'])){

	$epd_order = _epd_create_page_order('paypal');
	$price = $epd_order['price'];
	$num = $epd_order['trade_order_id'];

	$price = $price / $ice_payapl_api_rmb;
	$price = sprintf("%.2f",$price);

	$currencyCodeType='USD';
	$paymentType='Sale';

	$personName        = 'erphpdown';
	$SHIPTOSTREET      = 'wuhan';
	$SHIPTOCITY        = 'wuhan';
	$SHIPTOSTATE          = 'wuhan';
	$SHIPTOCOUNTRYCODE = '86';
	$SHIPTOZIP         = '430000';
	$L_NAME0           = __('网站充值','erphpdown');
	$L_AMT0            = $price;
	$L_QTY0            = 1;

	$returnURL =urlencode(ERPHPDOWN_URL.'/payment/paypal.php?currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType);
	$cancelURL =urlencode(get_bloginfo('url'));

	
	$itemamt = 0.00;
	$itemamt = $L_QTY0*$L_AMT0;
	$amt = $itemamt;
	$maxamt= $amt+25.00;
	$nvpstr="";
	$nvpHeader="";

	/*
	* Setting up the Shipping address details
	*/
	$shiptoAddress = "&SHIPTONAME=$personName&SHIPTOSTREET=$SHIPTOSTREET&SHIPTOCITY=$SHIPTOCITY&SHIPTOSTATE=$SHIPTOSTATE&SHIPTOCOUNTRYCODE=$SHIPTOCOUNTRYCODE&SHIPTOZIP=$SHIPTOZIP";

	$_SESSION["paypal_num"]=$num;
	$nvpstr="&L_AMT0=".$price."&SHIPPINGAMT=0.00&L_NAME0=".$L_NAME0."&L_NUMBER0=1000&L_QTY0=1&CURRENCYCODE=USD&NOSHIPPING=1&INVNUM=".$num."&AMT=".$price."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType."&PAYMENTACTION=".$paymentType;

	$nvpstr = $nvpHeader.$nvpstr;

	$resArray=hash_call("SetExpressCheckout",$nvpstr);
	$_SESSION['reshash']=$resArray;

	$ack = strtoupper($resArray["ACK"]);

	if($ack=="SUCCESS"){
		// Redirect to paypal.com here
		$token = urldecode($resArray["TOKEN"]);
		$payPalURL = PAYPAL_URL.$token;
		header("Location: ".$payPalURL);
	} 
	else
	{
		echo showPaypalError($resArray);
		exit;
	}
}
else
{
	/* At this point, the buyer has completed in authorizing payment
	at PayPal.  The script will now call PayPal with the details
	of the authorization, incuding any shipping information of the
	buyer.  Remember, the authorization is not a completed transaction
	at this state - the buyer still needs an additional step to finalize
	the transaction
	*/

	$token =urlencode( $_REQUEST['token']);

	/* Build a second API request to PayPal, using the token as the
	ID to get the details on the payment authorization
	*/
	$nvpstr="&TOKEN=".$token;

	$nvpstr = $nvpHeader.$nvpstr;
	/* Make the API call and store the results in an array.  If the
	call was a success, show the authorization details, and provide
	an action to complete the payment.  If failed, show the error
	*/
	$resArray=hash_call("GetExpressCheckoutDetails",$nvpstr);
	$_SESSION['reshash']=$resArray;
	$ack = strtoupper($resArray["ACK"]);

	if(($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') && get_option('ice_payapl_api_md5'))
	{

		$_SESSION['token']=$_REQUEST['token'];
		$_SESSION['payer_id'] = $_REQUEST['PayerID'];

		$_SESSION['paymentAmount']=$_REQUEST['paymentAmount'];
		$_SESSION['currCodeType']=$_REQUEST['currencyCodeType'];
		$_SESSION['paymentType']=$_REQUEST['paymentType'];

		$resArray=$_SESSION['reshash'];
		$_SESSION['TotalAmount']= $resArray['AMT'] + $resArray['SHIPDISCAMT'];

		ini_set('session.bug_compat_42',0);
		ini_set('session.bug_compat_warn',0);

		$token =urlencode( $_SESSION['token']);
		$paymentAmount =urlencode ($_SESSION['TotalAmount']);
		$paymentType = urlencode($_SESSION['paymentType']);
		$currCodeType = urlencode($_SESSION['currCodeType']);
		$payerID = urlencode($_SESSION['payer_id']);
		$serverName = urlencode($_SERVER['SERVER_NAME']);

		$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;
		/* Make the call to PayPal to finalize payment
		If an error occured, show the resulting errors
		*/
		$resArray=hash_call("DoExpressCheckoutPayment",$nvpstr);

		/* Display the API response back to the browser.
		If the response from PayPal was a success, display the response parameters'
		If the response was an error, display the errors received using APIError.php.
		*/
		$ack = strtoupper($resArray["ACK"]);
		if($ack != 'SUCCESS' && $ack != 'SUCCESSWITHWARNING')
		{
			echo showPaypalError($resArray);
			exit;
		}
		else
		{
			$num=$_SESSION["paypal_num"];
			$money=$_SESSION['TotalAmount']*$ice_payapl_api_rmb;
			$money=round($money,2);
			if(strstr($num,'MD') || strstr($num,'FK')){
				epd_set_wppay_success($num,$money,'paypal');
			}else{
				epd_set_order_success($num,$money,'paypal');
			}

			$re = get_option('erphp_url_front_success');
			if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){
			    $re = $_COOKIE['erphpdown_return'];
			}

			if($re){
				wp_redirect($re);
			}else{
				wp_die(__('充值成功','erphpdown'), __('友情提示','erphpdown'));
			}
			
		}
	}
	else
	{
		wp_die(showPaypalError($resArray));
	}
}
