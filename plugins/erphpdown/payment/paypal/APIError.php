<?php
/*************************************************
APIError.php

Displays error parameters.

Called by DoDirectPaymentReceipt.php, TransactionDetails.php,
GetExpressCheckoutDetails.php and DoExpressCheckoutPayment.php.

 *************************************************/
function showPaypalError($resArray)
{
	$msg="";
	$msg='<table width="100%"><tr><td colspan="2" class="header">The PayPal API has returned an error!</td></tr>';
	if(isset($_SESSION['curl_error_no']))
	{
		$errorCode= $_SESSION['curl_error_no'];
		$errorMessage= $_SESSION['curl_error_msg'];
	$msg.='<tr><td>错误代码:</td><td>'.$errorCode.'</td></tr><tr><td>错误信息:</td><td>'.$errorMessage.'</td></tr>';
	}
	else
	{
	foreach($resArray as $key => $value)
	{
		$msg.="<tr><td> $key:</td><td>$value</td>";
	}
	}
	$msg.='</table>';
	return $msg;
}
?>