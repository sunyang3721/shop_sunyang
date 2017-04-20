<?php
class queue {

    protected $handler;

    /* 加入队列 */
    public function add($type = 'email', $method, $params, $sort = 100) {
        $params = (is_array($params) && !empty($params)) ? json_encode($params) : $params;
        $data = array(
            'type'  => $type,
            'method' => $method,
            'params' => $params,
            'dateline' => TIMESTAMP,
            'sort' => (int) $sort
        );
        if($sort == 0){
            model('notify/queue','service')->config($data)->send();
        }else{
            model('notify/queue')->add($data);
        }
        return true;
	}

    public function update($id, $status) {
        return model('notify/queue')->where(array('id' => $id))->setField('status', 1);
    }

    public function send_fail($id, $status){
        return model('notify/queue')->where(array('id' => $id))->setField('status', -1);
    }

    public function run() {
        $queues = model('notify/queue')->where(array('status' => 0))->order("sort ASC, id DESC")->select();
        if(!$queues) return FALSE;
        foreach ($queues AS $queue) {
            if($queue['status'] == 0){
               $result = model('notify/queue','service')->config($queue)->send();
            }
        }
        return true;
    }

    public function get_handler() {
        return false;
        return $this->handler;
    }

    public function get() {
        global $members;
        print_r($members);
    }


}