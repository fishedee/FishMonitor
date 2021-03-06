<?php
	class Model{
		var $userId;
		var $data;
		var $machineIP;
		var $config;
		public function __construct($config){
			$this->data = array();

			//配置monitor
			$monitorConfig = $config->getMonitorConfig();
			if( isset($monitorConfig['user_id']) )
				$this->userId = $monitorConfig['user_id'];
			else
				throw new Exception('配置文件中缺少指定monitor的user_id');
			$this->machineIP = $config->getMachineIP();
		}

		private function onceCreate($id,$value,$info){
			if( isset($this->data[$id]) == true )
				return;
			$this->data[$id] = array(
				'metricName'=>$id,
				'value'=>$value,
				'unit'=>'None',
				'dimensions'=>array_merge(
					$info,
					array(
						'machineIP'=>$this->machineIP
					)
				),
				'timestamp'=>intval(microtime(true)*1000)
			);
		}

		public function updateValue($id,$value){
			$this->data[$id]['value'] = $value;
			$this->data[$id]['timestamp'] = intval(microtime(true)*1000);
		}

		public function add($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);
				
			$this->updateValue(
				$id,
				$this->data[$id]['value'] + $value
			);
		}

		public function max($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);
				
			$this->updateValue(
				$id,
				max($this->data[$id]['value'],$value)
			);
		}

		public function min($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);
				
			$this->updateValue(
				$id,
				min($this->data[$id]['value'],$value)
			);
		}

		public function set($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);

			$this->updateValue(
				$id,
				$value
			);
		}

		private function clear(){
			$this->data = array();
		}

		public function upload(){
			if( count($this->data) == 0 )
				return;
			$postData = array(
				'userId'=>$this->userId,
				'namespace'=>"acs/custom/".$this->userId,
				'metrics'=>json_encode(array_values($this->data))
			);
			log_message('debug','task monitor upload '.urldecode(http_build_query($postData)));
			$this->post(
				'http://open.cms.aliyun.com/metrics/put',
				$postData
			);
			$this->clear();
		}

		private function post($url,$data){
			$response = (new GuzzleHttp\Client())->post($url,array('form_params'=>$data,'http_errors' => false));
			if( $response->getStatusCode() != 200 )
				throw new Exception('错误码不是200,错误码是'.$response->getStatusCode()." ".$response->getBody()->getContents(),1);
		}

	};
?>