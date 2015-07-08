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
			$machineIP = $config->getMachineIP();
		}

		private function onceCreate($id,$value,$info){
			if( isset($this->data[$id]) == true )
				return;
			$this->data[$id] = array(
				'metricName'=>$id,
				'value'=>$value,
				'unit'=>'count',
				'dimensions'=>array_merge(
					$info,
					array(
						'machineIP'=>$this->machineIP
					)
				),
				'timestamp'=>time()
			);
		}

		public function add($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);
				
			$this->data[$id]['value'] += $value;
			$this->data[$id]['timestamp'] = time();
		}

		public function max($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);
				
			if( $value > $this->data[$id]['value'])
				$this->data[$id]['value'] = $value;
			$this->data[$id]['timestamp'] = time();
		}

		public function min($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);
				
			if( $value < $this->data[$id]['value'])
				$this->data[$id]['value'] = $value;
			$this->data[$id]['timestamp'] = time();
		}

		public function set($id,$value,$info=array()){
			$this->onceCreate($id,$value,$info);

			$this->data[$id]['value'] = $value;
			$this->data[$id]['timestamp'] = time();
		}

		private function clear(){
			$this->data = array();
		}

		public function upload(){
			if( count($this->data) == 0 )
				return;
			$postData = array(
				'userId'=>$this->userId,
				'namespace'=>"ACS/CUSTOM/".$this->userId,
				'metrics'=>json_encode(array_values($this->data))
			);
			$this->post(
				'http://open.cms.aliyun.com/metrics/put',
				http_build_query(($postData))
			);
			$this->clear();
		}

		private function post($url,$data){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt($curl, CURLOPT_TIMEOUT_MS , 5000);
			curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
			$data = curl_exec($curl);
			$headerData = curl_getinfo($curl);
			if( $data === false )
				throw new Exception('连接服务器失败'.curl_error($curl),1);
			if( $headerData['http_code'] != 200 )
				throw new Exception('错误码不是200,错误码是'.$headerData['http_code']." ".$data,1);
			curl_close($curl);
			return $data;
		}

	};
?>