<?php
define('PAYJS_KEY', get_option('erphpdown_payjs_appsecret'));
define('PAYJS_APPID', get_option('erphpdown_payjs_appid'));
class Payjs
{
    private $url = 'https://payjs.cn/api/native';
    private $url2 = 'https://payjs.cn/api/cashier';
    private $key = PAYJS_KEY;
    private $mchid = PAYJS_APPID;

    public function __construct($data=null) {
        $this->data = $data;
    }

    public function pay(){
        $data = $this->data;

        $data['mchid'] = $this->mchid;
        $data['sign'] = $this->sign($data);

        return $this->post($data, $this->url);
    }

    public function pay2(){
        $data = $this->data;

        $data['mchid'] = $this->mchid;
        $data['sign'] = $this->sign($data);

        return $this->buildRequestForm($data, $this->url2);
    }

    public function buildRequestForm($para, $action) {
        $sHtml = "<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <title>正在前往微信支付...</title>
    <style>input{display:none}</style>
</head><form id='payjssubmit' name='payjssubmit' action='".$action."' method='post' style='display:none'>";
        while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."<input type='submit' value='提交'></form>";
        $sHtml = $sHtml."<script>document.forms['payjssubmit'].submit();</script>";
        return $sHtml;
    }

    public function post($data, $url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $rst = curl_exec($ch);
        curl_close($ch);

        return $rst;
    }

    public function sign(array $attributes) {
        ksort($attributes);
        $sign = strtoupper(md5(urldecode(http_build_query($attributes)) . '&key=' . $this->key));
        return $sign;
    }
}