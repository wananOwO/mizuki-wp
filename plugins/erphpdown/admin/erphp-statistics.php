<div class="wrap">
    <h1>收入统计</h1><br>
    <?php
        global $wpdb, $wppay_table_name;
		$ice_proportion_alipay = get_option('ice_proportion_alipay');
		$ice_proportion_alipay = $ice_proportion_alipay?$ice_proportion_alipay:1;

		$ice_note = "ice_note=0";
		if(get_option("erphp_addon_card_total")){
			$ice_note = "(ice_note=0 or ice_note=6)";
		}

		$today_chong_money = $wpdb->get_row("SELECT SUM(ice_money) as cm, count(ice_id) as ct FROM $wpdb->icemoney WHERE ice_success>0 and ".$ice_note." and TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");
		$today_order_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->icealipay WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");
		$today_vip_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->vip WHERE TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");

		$yestoday_chong_money = $wpdb->get_row("SELECT SUM(ice_money) as cm, count(ice_id) as ct FROM $wpdb->icemoney WHERE ice_success>0 and ".$ice_note." and TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		$yestoday_order_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->icealipay WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		$yestoday_vip_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->vip WHERE TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		
		$month_chong_money = $wpdb->get_row("SELECT SUM(ice_money) as cm, count(ice_id) as ct FROM $wpdb->icemoney WHERE ice_success>0 and ".$ice_note." and DATE_FORMAT(ice_time, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')");
		$month_order_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->icealipay WHERE ice_success>0 and DATE_FORMAT(ice_time, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m') ");
		$month_vip_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->vip WHERE DATE_FORMAT(ice_time, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')");
		
		$year_chong_money = $wpdb->get_row("SELECT SUM(ice_money) as cm, count(ice_id) as ct FROM $wpdb->icemoney WHERE ice_success>0 and ".$ice_note." and YEAR(ice_time) = YEAR(CURDATE())");
		$year_order_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->icealipay WHERE ice_success>0 and YEAR(ice_time) = YEAR(CURDATE())");
		$year_vip_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->vip WHERE YEAR(ice_time) = YEAR(CURDATE())");

		echo '<div style="margin:0 -10px 20px;overflow:hidden">';
		
		echo '<div style="margin:0 10px 10px;float:left;width:calc(25% - 20px);border:1px solid #c3c4c7;padding:20px 30px;box-sizing:border-box">';
		echo '<div style="font-size:22px;margin-bottom:25px;">今日充值</div>
			  <div><a style="font-size:32px" href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($today_chong_money->cm?$today_chong_money->cm:'0')/$ice_proportion_alipay.' 元</a><span style="float:right;position:relative;top:5px;">'.($today_chong_money->ct?$today_chong_money->ct:'0').' 笔</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>文章</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($today_order_money->cm?$today_order_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($today_order_money->ct?$today_order_money->ct:'0').' 单</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>VIP</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-vip-items.php').'">'.($today_vip_money->cm?$today_vip_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($today_vip_money->ct?$today_vip_money->ct:'0').' 个</span></div>';
		echo '</div>';
		
		echo '<div style="margin:0 10px 10px;float:left;width:calc(25% - 20px);border:1px solid #c3c4c7;padding:20px 30px;box-sizing:border-box">';
		echo '<div style="font-size:22px;margin-bottom:25px;">昨日充值</div>
				<div><a style="font-size:32px" href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($yestoday_chong_money->cm?$yestoday_chong_money->cm:'0')/$ice_proportion_alipay.' 元</a><span style="float:right;position:relative;top:5px;">'.($yestoday_chong_money->ct?$yestoday_chong_money->ct:'0').' 笔</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>文章</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($yestoday_order_money->cm?$yestoday_order_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($yestoday_order_money->ct?$yestoday_order_money->ct:'0').' 单</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>VIP</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-vip-items.php').'">'.($yestoday_vip_money->cm?$yestoday_vip_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($yestoday_vip_money->ct?$yestoday_vip_money->ct:'0').' 个</span></div>';
		echo '</div>';
		
		echo '<div style="margin:0 10px 10px;float:left;width:calc(25% - 20px);border:1px solid #c3c4c7;padding:20px 30px;box-sizing:border-box">';
		echo '<div style="font-size:22px;margin-bottom:25px;">本月充值</div>
				<div><a style="font-size:32px" href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($month_chong_money->cm?$month_chong_money->cm:'0')/$ice_proportion_alipay.' 元</a><span style="float:right;position:relative;top:5px;">'.($month_chong_money->ct?$month_chong_money->ct:'0').' 笔</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>文章</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($month_order_money->cm?$month_order_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($month_order_money->ct?$month_order_money->ct:'0').' 单</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>VIP</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-vip-items.php').'">'.($month_vip_money->cm?$month_vip_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($month_vip_money->ct?$month_vip_money->ct:'0').' 个</span></div>';
		echo '</div>';
		
		
		echo '<div style="margin:0 10px 10px;float:left;width:calc(25% - 20px);border:1px solid #c3c4c7;padding:20px 30px;box-sizing:border-box">';
		echo '<div style="font-size:22px;margin-bottom:25px;">今年充值</div>
				<div><a style="font-size:32px" href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($year_chong_money->cm?$year_chong_money->cm:'0')/$ice_proportion_alipay.' 元</a><span style="float:right;position:relative;top:5px;">'.($year_chong_money->ct?$year_chong_money->ct:'0').' 笔</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>文章</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($year_order_money->cm?$year_order_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($year_order_money->ct?$year_order_money->ct:'0').' 单</span></div>';
		echo '<div style="border-top:1px solid #c3c4c7;padding-top:12px;margin:12px 0"><span>VIP</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-vip-items.php').'">'.($year_vip_money->cm?$year_vip_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($year_vip_money->ct?$year_vip_money->ct:'0').' 个</span></div>';
		echo '</div>';
		
		echo '</div>';

		$month_total = $wpdb->get_row("select
		    sum(case month(ice_time) when '1'  then ice_money else 0 end) as Jan,
		    sum(case month(ice_time) when '2'  then ice_money else 0 end) as Feb,
		    sum(case month(ice_time) when '3'  then ice_money else 0 end) as Mar,
		    sum(case month(ice_time) when '4'  then ice_money else 0 end) as Apr,
		    sum(case month(ice_time) when '5'  then ice_money else 0 end) as May,
		    sum(case month(ice_time) when '6'  then ice_money else 0 end) as June,
		    sum(case month(ice_time) when '7'  then ice_money else 0 end) as July,
		    sum(case month(ice_time) when '8'  then ice_money else 0 end) as Aug,
		    sum(case month(ice_time) when '9'  then ice_money else 0 end) as Sept,
		    sum(case month(ice_time) when '10' then ice_money else 0 end) as Oct,
		    sum(case month(ice_time) when '11' then ice_money else 0 end) as Nov,
		    sum(case month(ice_time) when '12' then ice_money else 0 end) as Dece
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and ice_success>0 and ".$ice_note.";");

		$month_total2 = $wpdb->get_row("select
		    sum(case month(ice_time) when '1'  then 1 else 0 end) as Jan,
		    sum(case month(ice_time) when '2'  then 1 else 0 end) as Feb,
		    sum(case month(ice_time) when '3'  then 1 else 0 end) as Mar,
		    sum(case month(ice_time) when '4'  then 1 else 0 end) as Apr,
		    sum(case month(ice_time) when '5'  then 1 else 0 end) as May,
		    sum(case month(ice_time) when '6'  then 1 else 0 end) as June,
		    sum(case month(ice_time) when '7'  then 1 else 0 end) as July,
		    sum(case month(ice_time) when '8'  then 1 else 0 end) as Aug,
		    sum(case month(ice_time) when '9'  then 1 else 0 end) as Sept,
		    sum(case month(ice_time) when '10' then 1 else 0 end) as Oct,
		    sum(case month(ice_time) when '11' then 1 else 0 end) as Nov,
		    sum(case month(ice_time) when '12' then 1 else 0 end) as Dece
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and ice_success>0 and ".$ice_note.";");

		$day_total = $wpdb->get_row("select
		    sum(case day(ice_time) when '1'  then ice_money else 0 end) as one,
		    sum(case day(ice_time) when '2'  then ice_money else 0 end) as two,
		    sum(case day(ice_time) when '3'  then ice_money else 0 end) as three,
		    sum(case day(ice_time) when '4'  then ice_money else 0 end) as four,
		    sum(case day(ice_time) when '5'  then ice_money else 0 end) as five,
		    sum(case day(ice_time) when '6'  then ice_money else 0 end) as six,
		    sum(case day(ice_time) when '7'  then ice_money else 0 end) as seven,
		    sum(case day(ice_time) when '8'  then ice_money else 0 end) as eight,
		    sum(case day(ice_time) when '9'  then ice_money else 0 end) as nine,
		    sum(case day(ice_time) when '10' then ice_money else 0 end) as ten,
		    sum(case day(ice_time) when '11' then ice_money else 0 end) as eleven,
		    sum(case day(ice_time) when '12' then ice_money else 0 end) as twelve,
		    sum(case day(ice_time) when '13' then ice_money else 0 end) as thirteen,
		    sum(case day(ice_time) when '14' then ice_money else 0 end) as fourteen,
		    sum(case day(ice_time) when '15' then ice_money else 0 end) as fifteen,
		    sum(case day(ice_time) when '16' then ice_money else 0 end) as sixteen,
		    sum(case day(ice_time) when '17' then ice_money else 0 end) as seventeen,
		    sum(case day(ice_time) when '18' then ice_money else 0 end) as eighteen,
		    sum(case day(ice_time) when '19' then ice_money else 0 end) as nineteen,
		    sum(case day(ice_time) when '20' then ice_money else 0 end) as twenty,
		    sum(case day(ice_time) when '21' then ice_money else 0 end) as twentyone,
		    sum(case day(ice_time) when '22' then ice_money else 0 end) as twentytwo,
		    sum(case day(ice_time) when '23' then ice_money else 0 end) as twentythree,
		    sum(case day(ice_time) when '24' then ice_money else 0 end) as twentyfour,
		    sum(case day(ice_time) when '25' then ice_money else 0 end) as twentyfive,
		    sum(case day(ice_time) when '26' then ice_money else 0 end) as twentysix,
		    sum(case day(ice_time) when '27' then ice_money else 0 end) as twentyseven,
		    sum(case day(ice_time) when '28' then ice_money else 0 end) as twentyeight,
		    sum(case day(ice_time) when '29' then ice_money else 0 end) as twentynine,
		    sum(case day(ice_time) when '30' then ice_money else 0 end) as thirty,
		    sum(case day(ice_time) when '31' then ice_money else 0 end) as thirtyone
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and month(ice_time)='".ltrim(date("m"),'0')."' and ice_success>0 and ".$ice_note.";");

		$day_total2 = $wpdb->get_row("select
		    sum(case day(ice_time) when '1'  then 1 else 0 end) as one,
		    sum(case day(ice_time) when '2'  then 1 else 0 end) as two,
		    sum(case day(ice_time) when '3'  then 1 else 0 end) as three,
		    sum(case day(ice_time) when '4'  then 1 else 0 end) as four,
		    sum(case day(ice_time) when '5'  then 1 else 0 end) as five,
		    sum(case day(ice_time) when '6'  then 1 else 0 end) as six,
		    sum(case day(ice_time) when '7'  then 1 else 0 end) as seven,
		    sum(case day(ice_time) when '8'  then 1 else 0 end) as eight,
		    sum(case day(ice_time) when '9'  then 1 else 0 end) as nine,
		    sum(case day(ice_time) when '10' then 1 else 0 end) as ten,
		    sum(case day(ice_time) when '11' then 1 else 0 end) as eleven,
		    sum(case day(ice_time) when '12' then 1 else 0 end) as twelve,
		    sum(case day(ice_time) when '13' then 1 else 0 end) as thirteen,
		    sum(case day(ice_time) when '14' then 1 else 0 end) as fourteen,
		    sum(case day(ice_time) when '15' then 1 else 0 end) as fifteen,
		    sum(case day(ice_time) when '16' then 1 else 0 end) as sixteen,
		    sum(case day(ice_time) when '17' then 1 else 0 end) as seventeen,
		    sum(case day(ice_time) when '18' then 1 else 0 end) as eighteen,
		    sum(case day(ice_time) when '19' then 1 else 0 end) as nineteen,
		    sum(case day(ice_time) when '20' then 1 else 0 end) as twenty,
		    sum(case day(ice_time) when '21' then 1 else 0 end) as twentyone,
		    sum(case day(ice_time) when '22' then 1 else 0 end) as twentytwo,
		    sum(case day(ice_time) when '23' then 1 else 0 end) as twentythree,
		    sum(case day(ice_time) when '24' then 1 else 0 end) as twentyfour,
		    sum(case day(ice_time) when '25' then 1 else 0 end) as twentyfive,
		    sum(case day(ice_time) when '26' then 1 else 0 end) as twentysix,
		    sum(case day(ice_time) when '27' then 1 else 0 end) as twentyseven,
		    sum(case day(ice_time) when '28' then 1 else 0 end) as twentyeight,
		    sum(case day(ice_time) when '29' then 1 else 0 end) as twentynine,
		    sum(case day(ice_time) when '30' then 1 else 0 end) as thirty,
		    sum(case day(ice_time) when '31' then 1 else 0 end) as thirtyone
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and month(ice_time)='".ltrim(date("m"),'0')."' and ice_success>0 and ".$ice_note.";");
?>
		<script src="<?php echo ERPHPDOWN_URL;?>/static/chart.js"></script>
		<div style="margin:0 -10px;overflow:hidden">
		    <div style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #c3c4c7;box-sizing:border-box"><canvas id="erphpdown_month_total" ></canvas></div>
		    <div style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #c3c4c7;box-sizing:border-box"><canvas id="erphpdown_year_total" ></canvas></div>
		</div>
		<script>
			var config = {
				type: 'bar',
				data: {
					labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
					datasets: [{
						label: '充值额(元)',
						backgroundColor: window.chartColors.red,
						borderColor: window.chartColors.red,
						data: [
							<?php echo $month_total->Jan / $ice_proportion_alipay;?>,
							<?php echo $month_total->Feb / $ice_proportion_alipay;?>,
							<?php echo $month_total->Mar / $ice_proportion_alipay;?>,
							<?php echo $month_total->Apr / $ice_proportion_alipay;?>,
							<?php echo $month_total->May / $ice_proportion_alipay;?>,
							<?php echo $month_total->June / $ice_proportion_alipay;?>,
							<?php echo $month_total->July / $ice_proportion_alipay;?>,
							<?php echo $month_total->Aug / $ice_proportion_alipay;?>,
							<?php echo $month_total->Sept / $ice_proportion_alipay;?>,
							<?php echo $month_total->Oct / $ice_proportion_alipay;?>,
							<?php echo $month_total->Nov / $ice_proportion_alipay;?>,
							<?php echo $month_total->Dece / $ice_proportion_alipay;?>
						],
						fill: false,
					}, {
						label: '订单数(笔)',
						fill: false,
						backgroundColor: window.chartColors.blue,
						borderColor: window.chartColors.blue,
						data: [
							<?php echo $month_total2->Jan?$month_total2->Jan:0;?>,
							<?php echo $month_total2->Feb?$month_total2->Feb:0;?>,
							<?php echo $month_total2->Mar?$month_total2->Mar:0;?>,
							<?php echo $month_total2->Apr?$month_total2->Apr:0;?>,
							<?php echo $month_total2->May?$month_total2->May:0;?>,
							<?php echo $month_total2->June?$month_total2->June:0;?>,
							<?php echo $month_total2->July?$month_total2->July:0;?>,
							<?php echo $month_total2->Aug?$month_total2->Aug:0;?>,
							<?php echo $month_total2->Sept?$month_total2->Sept:0;?>,
							<?php echo $month_total2->Oct?$month_total2->Oct:0;?>,
							<?php echo $month_total2->Nov?$month_total2->Nov:0;?>,
							<?php echo $month_total2->Dece?$month_total2->Dece:0;?>
						],
					}]
				},
				options: {
					responsive: true,
					title: {
						display: true,
						text: '今年收入统计'
					},
					tooltips: {
						mode: 'index',
						intersect: false,
					},
					hover: {
						mode: 'nearest',
						intersect: true
					}
				}
			};

			var config2 = {
				type: 'bar',
				data: {
					labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
					datasets: [{
						label: '充值额(元)',
						backgroundColor: window.chartColors.red,
						borderColor: window.chartColors.red,
						data: [
							<?php echo $day_total->one / $ice_proportion_alipay;?>,
							<?php echo $day_total->two / $ice_proportion_alipay;?>,
							<?php echo $day_total->three / $ice_proportion_alipay;?>,
							<?php echo $day_total->four / $ice_proportion_alipay;?>,
							<?php echo $day_total->five / $ice_proportion_alipay;?>,
							<?php echo $day_total->six / $ice_proportion_alipay;?>,
							<?php echo $day_total->seven / $ice_proportion_alipay;?>,
							<?php echo $day_total->eight / $ice_proportion_alipay;?>,
							<?php echo $day_total->nine / $ice_proportion_alipay;?>,
							<?php echo $day_total->ten / $ice_proportion_alipay;?>,
							<?php echo $day_total->eleven / $ice_proportion_alipay;?>,
							<?php echo $day_total->twelve / $ice_proportion_alipay;?>,
							<?php echo $day_total->thirteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->fourteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->fifteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->sixteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->seventeen / $ice_proportion_alipay;?>,
							<?php echo $day_total->eighteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->nineteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->twenty / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyone / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentytwo / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentythree / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyfour / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyfive / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentysix / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyseven / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyeight / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentynine / $ice_proportion_alipay;?>,
							<?php echo $day_total->thirty / $ice_proportion_alipay;?>,
							<?php echo $day_total->thirtyone / $ice_proportion_alipay;?>
						],
						fill: false,
					}, {
						label: '订单数(笔)',
						fill: false,
						backgroundColor: window.chartColors.blue,
						borderColor: window.chartColors.blue,
						data: [
							<?php echo $day_total2->one?$day_total2->one:0;?>,
							<?php echo $day_total2->two?$day_total2->two:0;?>,
							<?php echo $day_total2->three?$day_total2->three:0;?>,
							<?php echo $day_total2->four?$day_total2->four:0;?>,
							<?php echo $day_total2->five?$day_total2->five:0;?>,
							<?php echo $day_total2->six?$day_total2->six:0;?>,
							<?php echo $day_total2->seven?$day_total2->seven:0;?>,
							<?php echo $day_total2->eight?$day_total2->eight:0;?>,
							<?php echo $day_total2->nine?$day_total2->nine:0;?>,
							<?php echo $day_total2->ten?$day_total2->ten:0;?>,
							<?php echo $day_total2->eleven?$day_total2->eleven:0;?>,
							<?php echo $day_total2->twelve?$day_total2->twelve:0;?>,
							<?php echo $day_total2->thirteen?$day_total2->thirteen:0;?>,
							<?php echo $day_total2->fourteen?$day_total2->fourteen:0;?>,
							<?php echo $day_total2->fifteen?$day_total2->fifteen:0;?>,
							<?php echo $day_total2->sixteen?$day_total2->sixteen:0;?>,
							<?php echo $day_total2->seventeen?$day_total2->seventeen:0;?>,
							<?php echo $day_total2->eighteen?$day_total2->eighteen:0;?>,
							<?php echo $day_total2->nineteen?$day_total2->nineteen:0;?>,
							<?php echo $day_total2->twenty?$day_total2->twenty:0;?>,
							<?php echo $day_total2->twentyone?$day_total2->twentyone:0;?>,
							<?php echo $day_total2->twentytwo?$day_total2->twentytwo:0;?>,
							<?php echo $day_total2->twentythree?$day_total2->twentythree:0;?>,
							<?php echo $day_total2->twentyfour?$day_total2->twentyfour:0;?>,
							<?php echo $day_total2->twentyfive?$day_total2->twentyfive:0;?>,
							<?php echo $day_total2->twentysix?$day_total2->twentysix:0;?>,
							<?php echo $day_total2->twentyseven?$day_total2->twentyseven:0;?>,
							<?php echo $day_total2->twentyeight?$day_total2->twentyeight:0;?>,
							<?php echo $day_total2->twentynine?$day_total2->twentynine:0;?>,
							<?php echo $day_total2->thirty?$day_total2->thirty:0;?>,
							<?php echo $day_total2->thirtyone?$day_total2->thirtyone:0;?>
						],
					}]
				},
				options: {
					responsive: true,
					title: {
						display: true,
						text: '本月收入统计'
					},
					tooltips: {
						mode: 'index',
						intersect: false,
					},
					hover: {
						mode: 'nearest',
						intersect: true
					}
				}
			};

			window.onload = function() {
				var ctx = document.getElementById('erphpdown_year_total').getContext('2d');
				window.myLine = new Chart(ctx, config);

				var ctx2 = document.getElementById('erphpdown_month_total').getContext('2d');
				window.myLine2 = new Chart(ctx2, config2);
			};
		</script>
</div>