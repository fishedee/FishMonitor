<?php
class Nginx{
	var $config;
	public function __construct($config){
		$this->config = $config;
	}

	public function monitor(){
		$time = time() - 60;
		$result = array();
		$nginxTime = date('d/M/Y:H:i',$time);
		
		if( isset($this->config['monitor']['access']) ){
			$accessCount = exec("cat ".$this->config['config']['access_log_path'].' | grep "'.$nginxTime.'" | wc -l');
			$result[ $this->config['monitor']['access'] ] = $accessCount;
		}

		if( isset($this->config['monitor']['error']) ){
			$errorCount = exec("cat ".$this->config['config']['error_log_path'].' | grep "'.$nginxTime.'" | wc -l');
			$result[ $this->config['monitor']['error'] ] = $errorCount;
		}
		
		return $result;
	}
};
?>