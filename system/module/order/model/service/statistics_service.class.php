<?php
/**
 * 		订单统计服务层
 */
class statistics_service extends service {

	protected $where = array();
	protected $result = array();
	protected $payments_ch = array();

	public function _initialize() {
		$this->db_order = $this->load->table('order/order');
		// $this->db_order_sub = $this->load->table('order/order_sub');
		// $this->db_order_sku = $this->load->table('order/order_sku');
		// $this->db_order_return = $this->load->table('order/order_return');
		// $this->db_order_refund = $this->load->table('order/order_refund');
		// $this->db_district = $this->load->table('admin/district');
		$this->payments_ch = array(	'bank' => '支付宝网银直连',
						'alipay_escow' => '支付宝担保交易',
						'ws_wap' => '支付宝手机支付',
						'alipay' => '支付宝即时到账',
						'wechat_qr' => '微信扫码支付',
						'wechat_js' => '微信手机支付',
						'jdpay'    =>  '京东支付'
					);
	}

	/**
	 * 组装搜索条件
	 * @param  array  $params
	 *         				$params[buyer_id] : 会员主键id (int)
	 *         				$params[days] : 最近{多少}天 (int)
	 *         				$params[start_time] : 开始时间 (int, 时间戳)
	 *         				$params[end_time] : 结束时间 (int, 时间戳)
	 * @return [obj]
	 */
	public function build_sqlmap($params = array()) {
		if(isset($params['buyer_id']) && is_numeric($params['buyer_id'])) {
            $this->where['buyer_id'] = $params['buyer_id'];
        }
        if (isset($params['days']) && $params['days'] > 0) {
        	$days = $params['days'];
        	$days -= 1;
        	$this->where['search']['time'] = array('BETWEEN',array(strtotime("-{$params['days']}day",strtotime(date('Y-m-d 00:00:00'))) ,time()));
        	$this->where['search']['days'] = $params['days'];
        } else if (isset($params['start_time']) && isset($params['end_time'])) {
	        $this->where['search']['time'] = array('BETWEEN',array($params['start_time'] ,$params['end_time']));
			//两个时间戳之间的天数
	        $this->where['search']['days'] = round(($params['end_time'] - $params['start_time'])/86400);
        }
        return $this;
	}

	/**
	 * 销售统计 
	 * @return [result] (本日、本周、本月、本年[、日期搜索]):订单数,订单销售额,人均客单价,已取消订单
	 */
	public function sales() {
		// 按日期搜索
		if ($this->where['search']) {
			$days = $this->where['search']['days'];
			$search = $this->where['search']['time'];
			unset($this->where['search']);
			$sqlmap['status'] = 1;
			$sqlmap['system_time'] = $search;
			$field = "FROM_UNIXTIME(system_time,'%Y-%m-%d') days,SUM(real_amount) amount,count(distinct buyer_id) as peoples,count(id) as orders";
			$sqlquery = $this->db_order->where($sqlmap)->field($field)->group('days')->buildSql();
			$_searchs = $this->db_order->query($sqlquery);
			foreach ($_searchs as $k => $val) {
				$val['average'] = ($val['peoples']==0) ? '0.00' : sprintf("%.2f",$val['amount']/$val['peoples']);
				$_searchs[$val['days']] = $val;
				unset($_searchs[$k]);
			}
			for ($i = 0; $i <= $days; $i++) {
				$today = date('Y-m-d',strtotime("+{$i}day",$search[1][0]));
				$this->result['search']['dates'][$i] = $today;
				$_amounts[] = isset($_searchs[$today]['amount']) ? $_searchs[$today]['amount'] : '0.00';
				$_orders[] = isset($_searchs[$today]['orders']) ? $_searchs[$today]['orders'] : '0';
				$_averages[] = isset($_searchs[$today]['average']) ? $_searchs[$today]['average'] : '0.00';
			}
			$this->result['search']['series']['amounts']  = $_amounts;
			$this->result['search']['series']['orders']   = $_orders;
			$this->result['search']['series']['averages'] = $_averages;
		}

		// 本日查询条件
		$start = strtotime(date('Y-m-d 00:00:00'));
		$end   = strtotime(date('Y-m-d 23:59:59'));
		$today = array('BETWEEN',array($start, $end));
		// 本周查询条件
		$start = mktime(0, 0, 0,date("m"),date("d")-date("w")+1,date("Y"));
		$end   = mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));
		$week  = array('BETWEEN',array($start ,$end));
		// 本月查询条件
		$start = strtotime(date('Y-m-01 00:00:00'));
		$end   = strtotime(date('Y-m-d H:i:s'));
		$month = array('BETWEEN',array($start ,$end));
		// 本年查询条件
		$start = strtotime(date('Y-01-01 00:00:00'));
		$end   = strtotime(date('Y-m-d H:i:s'));
		$year  = array('BETWEEN',array($start ,$end));

		/* 订单数 */
		$sqlmap = $this->where;
		$sqlmap['system_time'] = $today;
		$this->result['today']['orders'] = (int) $this->db_order->where($sqlmap)->count();

		$sqlmap['system_time'] = $week;
		$this->result['week']['orders'] = (int) $this->db_order->where($sqlmap)->count();

		$sqlmap['system_time'] = $month;
		$this->result['month']['orders'] = (int) $this->db_order->where($sqlmap)->count();

		$sqlmap['system_time'] = $year;
		$this->result['year']['orders'] = (int) $this->db_order->where($sqlmap)->count();

		/* 订单销售额 */ 
		$sqlmap = $this->where;
		$sqlmap['status'] = 1;
		$sqlmap['system_time'] = $today;
		$this->result['today']['amount'] = sprintf("%.2f",$this->db_order->where($sqlmap)->sum("real_amount"));

		$sqlmap['system_time'] = $week;
		$this->result['week']['amount'] = sprintf("%.2f",$this->db_order->where($sqlmap)->sum("real_amount"));

		$sqlmap['system_time'] = $month;
		$this->result['month']['amount'] = sprintf("%.2f",$this->db_order->where($sqlmap)->sum("real_amount"));

		$sqlmap['system_time'] = $year;
		$this->result['year']['amount'] = sprintf("%.2f",$this->db_order->where($sqlmap)->sum("real_amount"));

		/* 人均客单价 */
		$sqlmap = $this->where;
		$sqlmap['system_time'] = $today;
		$peoples = (int) $this->db_order->where($sqlmap)->count('distinct buyer_id');
		$this->result['today']['average'] = ($peoples==0) ? '0.00' : sprintf("%.2f",$this->result['today']['amount']/$peoples);

		$sqlmap['system_time'] = $week;
		$peoples = (int) $this->db_order->where($sqlmap)->count('distinct buyer_id');
		$data = $this->db_order->where($sqlmap)->select();
		$this->result['week']['average'] = ($peoples==0) ? '0.00' : sprintf("%.2f",$this->result['week']['amount']/$peoples);

		$sqlmap['system_time'] = $month;
		$peoples = $this->db_order->where($sqlmap)->count('distinct buyer_id');
		$this->result['month']['average'] = ($peoples==0) ? '0.00' : sprintf("%.2f",$this->result['month']['amount']/$peoples);

		$sqlmap['system_time'] = $year;
		$peoples = (int) $this->db_order->where($sqlmap)->count('distinct buyer_id');
		$this->result['year']['average'] = ($peoples==0) ? '0.00' : sprintf("%.2f",$this->result['year']['amount']/$peoples);

		/* 已取消订单 */
		$sqlmap = $this->where;
		$sqlmap['status'] = array('GT',1);
		$sqlmap['system_time'] = $today;
		$this->result['today']['cancels'] = (int) $this->db_order->where($sqlmap)->count();

		$sqlmap['system_time'] = $week;
		$this->result['week']['cancels'] = (int) $this->db_order->where($sqlmap)->count();

		$sqlmap['system_time'] = $month;
		$this->result['month']['cancels'] = (int) $this->db_order->where($sqlmap)->count();

		$sqlmap['system_time'] = $year;
		$this->result['year']['cancels'] = (int) $this->db_order->where($sqlmap)->count();
		return $this;
	}

	/* 地区订单统计 */
	public function districts() {
		// 组装省级地区为一维数组
		$districts = $this->load->service('admin/district')->get_children(0);
		$arr = $areas = array();
		foreach ($districts as $k => $v) {
			if ($v['id'] == 820000) {
				$macao = $v;
				continue;
			}
			$arr[] = $this->load->service('admin/district')->get_children($v['id']);
		}
		foreach ($arr as $val) {
			foreach ($val as $v) {
				$areas[] = $v;
			}
		}
		if ($macao) $areas[] = $macao;
		foreach ($areas as $key => $area) {
			$sqlmap['status'] = 1;
			$sqlmap['_string'] = "FIND_IN_SET($area[id], `address_district_ids`)";
			$areas[$key]['value'] = (int) $this->db_order->where($sqlmap)->count();
		}
		$this->result['districts'] = $areas;
		return $this;
	}

	/* 支付方式订单统计 */
	public function payments() {
		$payments = $this->load->service('pay/payment')->fetch_all();
		$payments = array_keys($payments);
		$pays_count = array();
		foreach ($payments as $k => $code) {
			$pays_count[$k]['code'] = $code;
			$pays_count[$k]['name'] = $this->payments_ch[$code];
			$pays_count[$k]['value'] = (int) $this->db_order->where(array('pay_method' => $code))->count();
		}
		$this->result['payments'] = $pays_count;
		return $this;
	}

	/**
     * 输出统计结果
     * @param  string $fun_name 要统计的方法名（多个用 ，分割），默认统计所有结果
     * @return [result]
     */
    public function output($fun_name = '') {
        if (empty($fun_name)) {
            $this->sales()->districts()->payments();
        } else {
        	$fun_names = explode(',', $fun_name);
        	foreach ($fun_names as $name) {
        		if (method_exists($this,$name)) {
        			$this->$name();
        		}
        	}
        }
        return $this->result;
    }
    public function get_data(){
    	$datas = array();
		/* 订单提醒 */
		$datas['orders'] = $this->load->table('order/order')->out_counts();
		/* 商品管理 */
		$datas['goods']['goods_in_sales'] = $this->load->service('goods/goods_spu')->count_spu_info(1);
		$datas['goods']['goods_load_online'] = $this->load->service('goods/goods_spu')->count_spu_info(0);
		$datas['goods']['goods_number_warning'] = $this->load->service('goods/goods_spu')->count_spu_info(2);
		/* 待处理咨询 */
		$datas['consult_load_do'] = $this->load->service('goods/goods_consult')->handle();
		/* 资金管理 */
		$datas['sales'] = $this->output('sales');
		/* 注册会员总数 */
		$datas['member_total'] = $this->load->table('member/member')->count();
		/* 数据库大小 */
		$querysql = "select round(sum(DATA_LENGTH/1024/1024)+sum(DATA_LENGTH/1024/1024),2) as db_length from information_schema.tables where table_schema='".config('DB_NAME')."'";
		$datas['dbsize'] = $this->load->table('member/member')->query($querysql);
		return $datas;
    }

}