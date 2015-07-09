<?php
	class ServiceTask{
		var $name;
		var $service;
		var $port;
		public function __construct($config,$port){
			if( isset($config['name']) == false )
				throw new Exception('缺少服务的name字段',1);
			$name = $config['name'];
			
			$servicePath = dirname(__FILE__).'/'.$name.'.php';
			if( file_exists($servicePath) == false )
				throw new Exception('不存在服务'.$name);
			require_once($servicePath);
			if( class_exists($name) == false )
				throw new Exception('服务文件中不存在同名的类名'.$name);

			$this->service = new $name($config);
			$this->name = $name;
			$this->port = $port;
		}

		public function onWorkerStart(){
			log_message('debug','service '.$this->name.' task start!');
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