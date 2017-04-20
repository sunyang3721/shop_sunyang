<?php
class index_control extends init_control
{
    public function _initialize()
    {
        parent::_initialize();
        $this->service = $this->load->service('goods/goods_sku');
        $this->cate_service = $this->load->service('goods/goods_category');
        $this->brand_service = $this->load->service('goods/brand');
    }

    public function index()
    {
        $setting = model('admin/setting','service')->get();
        $seos = $setting['seos'];
        $site_title = $setting['site_name'] . ' - ' . $seos['header_title_add'];
        $site_keywords = $seos['header_keywords'];
        $site_description = $seos['header_description'];
        $site_rewrite_other = $seos['header_other'];
        $SEO = seo($site_title, $site_keywords, $site_description, $site_rewrite_other, true);
        $this->load->librarys('View')->assign('SEO',$SEO)->assign('setting',$setting)->assign('site_rewrite_other',$site_rewrite_other)->display('index');
    }

    /**
     * [lists 前台商品列表]
     * @param  [type] $id [分类id]
     * @return [type]     [description]
     */
    public function lists()
    {
        $category = $this->cate_service->create_category($_GET['id']);
        if (!$category) {
            showmessage($this->cate_service->error, url('index'));
        }
        $cat_form = $this->cate_service->detail($category['parent_id'],'name');
        $title = is_null($cat_form) ? $category['name'] : $category['name'] . ' - ' . $cat_form;
        $SEO = seo($title, $category['keywords'], $category['descript']);
        $brands = $this->cate_service->get_brand_info($_GET['id']);
        $grades = $this->cate_service->get_cate_grades($_GET['id']);
        $result = $this->service->create_sqlmap($_GET);
        $this->load->librarys('View')->assign('SEO',$SEO)->assign('brands',$brands)->assign('grades',$grades)->assign('result',$result)->assign('category',$category)->display('lists');
    }

    /**
     * [detail 前台商品详情页]
     * @param  [type] $id [商品id]
     * @return [type] [description]
     */
    public function detail()
    {
        $goods = $this->service->detail($_GET['sku_id'], FALSE);
        if (!$goods) {
            showmessage($this->service->error, url('index'));
        }
        if ($goods['prom_type'] == 'goods' && $goods['prom_id'] > 0) {
            $goods_proms = $this->load->service('promotion/promotion_goods')->find(array('id' => $goods['prom_id']));
            $counts = 0;
            foreach ($goods_proms['rules'] as $key => $value) {
              if($value) $counts++;
              switch ($value['type']) {
                    case 'amount_discount':
                    $type = '满额立减';
                    break;
                    case 'number_discount':
                    $type = '满件立减';
                    break;
                    case 'amount_give':
                    $type = '满额送礼';
                    break;
                    case 'number_give':
                    $type = '满件送礼';
                    break;
                  default:
                      break;
              }
              $goods_proms['rules'][$key]['subtitle'] = $type;
            }
            $this->load->librarys('View')->assign('counts',$counts)->assign('goods_proms',$goods_proms);
        }
        $count = $this->load->service('comment/comment')->get_count($goods['spu_id']);
        $title = $goods['sku_name'] . ' - ' . $goods['cat_name'];
        $SEO = seo($title, $goods['keyword'], $goods['description']);
        $this->service->_history($_GET['sku_id']);
        $this->service->inc_hits($_GET['sku_id']);
        runhook('goods_detail_extra', $goods);
        $this->load->librarys('View')->assign('count',$count)->assign('SEO',$SEO)->assign('goods',$goods)->display('detail');
     }

     public function ajax_get_favorite(){
        $favorite = $this->service->is_favorite($this->member['id'], $_GET['sku_id']);
        showmessage('获取收藏状态成功','',$favorite);
     }
    /**
     * 商品快照
     * @param sku_id
     * @param order_sku_id 订单sku_id
     */
    public function snapshot()
    {
        helper('order/function');
        $info = $this->load->service('order/order_sku')->detail($_GET['order_sku_id']);
        $sales = $this->service->getField('sales',array('sku_id' => $_GET['sku_id']));
        $goods = json_decode($info['sku_info'], true);
        $spec = json_decode($goods['sku_spec'], true);
        $img_list = json_decode($goods['img_list']);
        $SEO = seo('订单快照 - ' . $goods['sku_name']);
        $this->service->_history($_GET['sku_id']);
        $this->service->inc_hits($_GET['sku_id']);
        $this->load->librarys('View')->assign('info',$info)->assign('sales',$sales)->assign('goods',$goods)->assign('spec',$spec)->assign('img_list',$img_list)->assign('SEO',$SEO)->display('snapshot');
    }

    /**
     * [brand_list 品牌详情]
     * @return [type] [description]
     */
    public function brand_list()
    {
        $brand = $this->brand_service->get_brand_by_id($_GET['id']);
        if (!$brand) {
            showmessage($this->brand_service->error);
        }
        $SEO = seo($brand['name'], '', $brand['descript']);
        $result = $this->service->create_sqlmap($_GET);
        $this->load->librarys('View')->assign('brand',$brand)->assign('result',$result)->assign('SEO',$SEO)->display('brand_list');
    }

    /**
     * [category_lists wap分类列表]
     * @return [type] [description]
     */
    public function category_lists()
    {
        $SEO = seo('全部分类');
        $this->load->librarys('View')->assign('SEO',$SEO)->display('classify');
    }

    /**
     * [ajax_like 猜你喜欢]
     * @return [type] [description]
     */
    public function ajax_goods()
    {
        $sqlmap = array();
        if ($_GET['order']) {
            $sqlmap['order'] = $_GET['order'] == 'rand' ? 'rand()' : ($_GET['order'] == 'sales' ? $sqlmap['order'] = 'sales desc' : $_GET['order']);
        } else {
            $sqlmap['order'] = 'sort asc,sku_id desc';
        }
        if ($_GET['statusext']) {
            $sqlmap['status_ext'] = $_GET['statusext'];
        }
        if ($_GET['catid'] > 0) {
            $sqlmap['catid'] = $_GET['catid'];
        }
        if ($_GET['limit']) {
            $options['limit'] = $_GET['limit'];
        } else {
            $options['limit'] = 5;
        }
        $result = $this->service->lists($sqlmap, $options);
        foreach ($result['lists'] as $key => $value) {
            $result['lists'][$key]['thumb'] = thumb($value['thumb'], $_GET['length'], $_GET['length']);
        }
        $this->load->librarys('View')->assign('result',$result);
        $result = $this->load->librarys('View')->get('result');
        echo json_encode($result);
    }

    /**
     * [html_load html加载完毕后执行]
     * @return [type] [description]
     */
    public function html_load()
    {
        runhook('html_load');
    }

    /** [get_lists wap获取商品列表]
     * @return [type] [description]
     */
    public function get_lists()
    {
        $sqlmap = $options = array();
        $sqlmap = $this->build_goods_map($_GET);
        $options['limit'] = $_GET['limit'] ? $_GET['limit'] : 5;
        $options['page'] = $_GET['page'] ? $_GET['page'] : 1;
        $result = $this->service->lists($sqlmap, $options);
        $this->load->librarys('View')->assign('result',$result);
        $result = $this->load->librarys('View')->get('result');
        echo json_encode($result);
    }

    /** [build_goods_map GET]
     * @return [type] [description]
     */
    private function build_goods_map($param)
    {
        $sqlmap = array();
        $sqlmap['catid'] = $param['id'];
        if ($param['map']['attr']) {
            $sqlmap['goods_ids'] = $this->service->goods_attr_screen($param['map']['attr']);
        }
        if ($param['keyword']) {
            $search = $this->service->search($param);
            unset($sqlmap['catid']);
            $sqlmap['goods_ids'] = $search['_goods_ids'];
        }
        if ($param['map']['price']) {
            $sqlmap['price'] = $param['map']['price'];
        }
        if ($param['map']['brand_id']) {
            $sqlmap['brand_id'] = $param['map']['brand_id'];
        }
        $sqlmap['order'] = $param['sort'] ? $param['sort'] : '';
        return $sqlmap;
    }

    /**
     * [get_consult 获取商品咨询]
     * @return [type] [description]
     */
    public function get_consult()
    {
        $sqlmap = array();
        $sqlmap['spu_id'] = $_GET['spu_id'];
        $options['limit'] = $_GET['limit'] ? $_GET['limit'] : 5;
        $options['page'] = $_GET['page'] ? $_GET['page'] : 1;
        $result = $this->load->service('goods/goods_consult')->lists($sqlmap, $options);
        $this->load->librarys('View')->assign('result',$result);
        $result = $this->load->librarys('View')->get('result');
        echo json_encode($result);
    }
}