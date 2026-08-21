<?php
if ( !defined('ABSPATH') ) {exit;}
$user_Info   = wp_get_current_user();

if(get_option('erphp_mycred') == 'yes') $mycred_core = get_option('mycred_pref_core');
/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
if(isset($_POST['mycred_to_erphp']) && plugin_check_cred())
{
	if(!is_numeric($_POST['mycred_to_erphp']) || $_POST['mycred_to_erphp'] <= 0)
	{
		echo '<div class="error settings-error"><p>请输入一个正数！</p></div>';
	}
	elseif(floatval(mycred_get_users_cred( $user_Info->ID )) < floatval($_POST['mycred_to_erphp']*get_option('erphp_to_mycred')))
	{
		$mycred_core = get_option('mycred_pref_core');
		echo '<div class="error settings-error"><p>mycred剩余'.$mycred_core['name']['plural'].'不足！</p></div>';
	}
	else
	{
		mycred_add( '兑换', $user_Info->ID, '-'.$_POST['mycred_to_erphp']*get_option('erphp_to_mycred'), '兑换扣除%plural%!', date("Y-m-d H:i:s") );
		$money = $_POST['mycred_to_erphp'];
		if(addUserMoney($user_Info->ID, $money))
		{
			$sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
			VALUES ('$money','".date("y").mt_rand(10000000,99999999)."','".$user_Info->ID."','".date("Y-m-d H:i:s")."',1,'4','".date("Y-m-d H:i:s")."','')";
			$wpdb->query($sql);
			echo '<div class="updated settings-error"><p>兑换成功！</p></div>';
		}
		else
		{
			echo '<div class="error settings-error"><p>兑换失败，如有疑问请联系管理员处理！</p></div>';
		}
	}
}

$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_Info->ID);
if(!$userMoney)
{
	$okMoney=0;
}
else 
{
	$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
}
?>
<div class="wrap">

        <h2>我的mycred</h2>
        <form action="" method="post" onsubmit="return checkFm();">
        <table class="form-table">
            <?php if(get_option('erphp_mycred') == 'yes' && plugin_check_cred()){?>
            <tr>
                <td valign="top" width="20%"><strong>mycred剩余<?php echo $mycred_core['name']['plural'];?>：</strong><br />
                </td>
                <td>
                 <?php echo mycred_get_users_cred( $user_Info->ID )?>&nbsp;<?php echo $mycred_core['name']['plural']?>
                 &nbsp;&nbsp;兑换成<input style="width:100px" type="text" id="mycred_to_erphp" name="mycred_to_erphp" maxlength="50" size="50"/><?php echo get_option('ice_name_alipay')?>
                 &nbsp;&nbsp;<input type="submit" name="Submit" value="兑换" class="button-primary" onclick="return confirm('确认兑换成<?php echo get_option('ice_name_alipay')?>?');"/>（兑换规则：<?php echo get_option('erphp_to_mycred').$mycred_core['name']['plural']?> = 1<?php echo get_option('ice_name_alipay')?>）
                </td>
            </tr>
            <?php }?>
    </table></form>
    <script type="text/javascript">
		function checkFm()
		{
			if(document.getElementById("mycred_to_erphp").value=="")
			{
				alert('请输入金额');
				return false;
			}
		
		}
	</script>
        <br /> <br />
			</div>