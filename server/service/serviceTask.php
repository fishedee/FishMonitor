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
			log_message('debug','task '.$this->name.' service start!');
			$port = $this->port;
			\Workerman\Lib\Timer::add(60, function()use($port){
				$result = $this->service->monitor();
				log_message('debug','task '.$this->name.' service result : '.json_encode($result) );
		    	foreach( $result as $key=>$value ){
					$url = 'http://localhost:'.$port.'/set';
					$data = array(
						'id'=>$key,
						'value'=>$value
					);
					(new GuzzleHttp\Client())->get($url,array('query'=>$data));
				}
		    });
		}

		public function onMessage(){

		}
	};
?>