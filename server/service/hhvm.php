<?php
class Hhvm{
	var $config;
	public function __construct($config){
		$this->config = $config;
	}

	public function monitor(){
		$time = time();
		$result = array();
		$hhvmTime = date('M j H:i',$time);
	
		if( isset($this->config['target']['error']) ){
			$errorCount = exec("cat ".$this->config['error_log_path'].' | grep "'.$hhvmTime.'" | wc -c');
			$result[ $this->config['target']['error'] ] = $errorCount;
		}


		$data = json_decode(
			file_get_contents('http://localhost:'.$this->config['admin_port'].'/check-health'),
			true
		);

		if( isset($this->config['target']['load']) && isset($data['load']) ){
			$result[ $this->config['target']['load'] ] = $data['load'];
		}
		
		if( isset($this->config['target']['queued']) && isset($data['queued']) ){
			$result[ $this->config['target']['queued'] ] = $data['queued'];
		}
		
		return $result;
	}
};
?>