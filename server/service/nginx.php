<?php
class Nginx{
	var $config;
	public function __construct($config){
		$this->config = $config;
	}

	public function monitor(){
		$time = time();
		$result = array();
		$nginxTime = date('d/M/Y:H:i',$time);
		
		if( isset($this->config['target']['access']) ){
			$accessCount = exec("cat ".$this->config['access_log_path'].' | grep "'.$nginxTime.'" | wc -c');
			$result[ $this->config['target']['access'] ] = $accessCount;
		}

		if( isset($this->config['target']['error']) ){
			$errorCount = exec("cat ".$this->config['error_log_path'].' | grep "'.$nginxTime.'" | wc -c');
			$result[ $this->config['target']['error'] ] = $errorCount;
		}
		
		return $result;
	}
};
?>