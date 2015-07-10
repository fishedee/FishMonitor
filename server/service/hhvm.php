<?php
class Hhvm{
	var $config;
	public function __construct($config){
		$this->config = $config;
	}

	public function monitor(){
		$time = time() - 60;
		$result = array();
		$hhvmTime = date('M j H:i',$time);
	
		if( isset($this->config['monitor']['error']) ){
			$errorCount = exec("cat ".$this->config['config']['error_log_path'].' | grep "'.$hhvmTime.'" | wc -l');
			$result[ $this->config['monitor']['error'] ] = $errorCount;
		}


		$data = json_decode(
			file_get_contents('http://localhost:'.$this->config['config']['admin_port'].'/check-health'),
			true
		);

		if( isset($this->config['monitor']['load']) && isset($data['load']) ){
			$result[ $this->config['monitor']['load'] ] = $data['load'];
		}
		
		if( isset($this->config['monitor']['queued']) && isset($data['queued']) ){
			$result[ $this->config['monitor']['queued'] ] = $data['queued'];
		}
		
		return $result;
	}
};
?>