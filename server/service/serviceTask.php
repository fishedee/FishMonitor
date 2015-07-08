<?php
	class ServiceTask{
		var $service;
		var $port;
		public function __construct($name,$config,$port){
			$servicePath = dirname(__FILE__).'/'.$name.'.php';
			if( file_exists($servicePath) == false )
				throw new Exception('不存在服务'.$name);
			require_once($servicePath);
			if( class_exists($name) == false )
				throw new Exception('服务文件中不存在同名的类名'.$name);

			$this->service = new $name($config);
			$this->port = $port;
		}

		public function onWorkerStart(){
			$port = $this->port;
			\Workerman\Lib\Timer::add(5, function()use($port){
				$result = $this->service->monitor();
		    	foreach( $result as $key=>$value ){
					$url = 'http://localhost:'.$port.'/set?';
					$url .= http_build_query(array(
						'id'=>$key,
						'value'=>$value
					));
					file_get_contents($url);
				}
		    });
		}

		public function onMessage(){

		}
	};
?>