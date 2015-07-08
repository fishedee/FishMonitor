<?php
	require_once(dirname(__FILE__).'/controller.php');
	class MonitorTask{

		var $controller;

		public function __construct($config){
			$this->controller = new Controller($config);
		}

		public function onWorkerStart(){
			sleep(50);
			$controller = $this->controller;
			\Workerman\Lib\Timer::add(60, function()use($controller){
				try{
		        	$controller->refresh();
		    	}catch(Exception $e){
		    		log_message('error',"refresh error".$e->getMessage());
		    	}
		    });
		}

		public function onMessage($connection, $data){
			try{
				$uri = $_SERVER['REQUEST_URI'];
				if( strpos($uri,'?') !== false )
					$uri = substr($uri,0,strpos($uri,'?'));
				$uri = explode('/',$uri);
				if( isset($uri[1]) == false )
					throw new Exception('缺少操作的uri',1);
				$method = $uri[1];
				if( method_exists($this->controller, $method) == false )
					throw new Exception('不存在的操作，路径为：'.$_SERVER['REQUEST_URI'],1);

				$this->controller->$method();

			    $connection->send(json_encode(array(
			    	'code'=>0,
			    	'msg'=>'',
			    	'data'=>null
			    ),JSON_UNESCAPED_UNICODE));
			}catch(Exception $e){
				$connection->send(json_encode(array(
			    	'code'=>$e->getCode(),
			    	'msg'=>$e->getMessage(),
			    	'data'=>null
			    ),JSON_UNESCAPED_UNICODE));
			    log_message('error',"handle uri error ".$e->getMessage());
			}
		}
	};
?>