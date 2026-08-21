<?php
require_once('../../../../../wp-load.php');
require_once('init.php');
global $wpdb;
$erphpdown_stripe_pk  = get_option('erphpdown_stripe_pk');
$erphpdown_stripe_sk  = get_option('erphpdown_stripe_sk');
$erphpdown_stripe_hk  = get_option('erphpdown_stripe_hk');
if($erphpdown_stripe_sk && $erphpdown_stripe_hk){
	\Stripe\Stripe::setApiKey($erphpdown_stripe_sk);
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $erphpdown_stripe_hk
        );
    } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
	  // Invalid signature
	  http_response_code(400);
	  exit();
	}

    // Handle the event
    switch ($event->type) {
        case 'charge.succeeded':
            $succeeded = $event->data->object;
            //$content = "=========".date('Y-m-d H:i:s',time())."==========\r\n";
            //$content .= json_encode($succeeded);
            //file_put_contents('success.log',$content . "\r\n",FILE_APPEND);

            if ($succeeded->status == 'succeeded'){
                //$payment_intent = $succeeded->payment_intent;

                $out_trade_no = $succeeded->metadata->order_id;
        		$total_fee = $succeeded->amount *0.01;

                if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
					epd_set_wppay_success($out_trade_no,$total_fee,'stripe');
				}else{
					epd_set_order_success($out_trade_no,$total_fee,'stripe');
				}
            }
            break;
        default:
            http_response_code(400);
            exit();
            break;
    }
    http_response_code(200);
}else{
	http_response_code(400);
}
exit;