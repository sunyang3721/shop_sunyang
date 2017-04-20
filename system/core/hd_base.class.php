<?php
abstract class hd_base
{
	public function __construct() {
        if(method_exists($this,'_initialize')) $this->_initialize();
	}

	public function __get($name) {
		if($name == 'load'){
			return hd_load::getInstance();
		}
		return $this->$name;
	}
    
    public function __set($name, $value) {
		$this->$name = $value;
	}

	public function __call($name,$parameters) {
		throw new Exception('Class "'.get_class($this).'" does not have a method named "'.$name.'".');
	}

	public function __toString() {
		return get_class($this);
	}

	public function __invoke() {
		return get_class($this);
	}

}


// abstract class HD_Base {
	
//     public function __construct() {
//         if(method_exists($this,'_initialize'))
//             $this->_initialize();
//     }
    
// 	public function __get($name) {
// 		if($name == 'load'){
// 			return HD_Load::getInstance();
// 		}
// 		return $this->$name;
// 	}
//     public function __set($name, $value) {
// 		$this->$name = $value;
// 	}

// 	public function __call($name,$parameters) {
// 		throw new Exception('Class "'.get_class($this).'" does not have a method named "'.$name.'".');
// 	}
    
// 	public function __toString() {
// 		return get_class($this);
// 	}

// 	public function __invoke() {
// 		return get_class($this);
// 	}
// }
