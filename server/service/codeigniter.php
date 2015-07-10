<?php
class CodeIgniter{
	var $config;
	public function __construct($config){
		$this->config = $config;
	}

	public function monitor(){
		$time = time() - 60;
		$fileName = '"'.$this->config['config']['log_path'].'log-'.date('Y-m-d',$time).'.php'.'"';
		$ciTime = '"'.date('Y-m-d H:i',$time).'"';
		$result = array();
	
		if( isset($this->config['monitor']['access']) ){
			$count = exec("cat $fileName | grep $ciTime | grep INFO | grep \"Final output\" | wc -l");
			$result[ $this->config['monitor']['access'] ] = $count;
		}

		if( isset($this->config['monitor']['error']) ){
			$count = exec("cat $fileName | grep $ciTime | grep ERROR | wc -l");
			$result[ $this->config['monitor']['error'] ] = $count;
		}

		if( isset($this->config['monitor']['avg_execution_time']) ){
			$count = exec("cat $fileName | grep $ciTime | grep \"Total execution\" | cut -d \" \" -f 11 | awk '{ SUM += $1 ; COUNT += 1 } END { print SUM/COUNT }'");
			$result[ $this->config['monitor']['avg_execution_time'] ] = $count;
		}

		if( isset($this->config['monitor']['max_execution_time']) ){
			$count = exec("cat $fileName | grep $ciTime | grep \"Total execution\" | cut -d \" \" -f 11 | awk '{ SUM = $1 > SUM ? $1:SUM } END { print SUM }'");
			$result[ $this->config['monitor']['max_execution_time'] ] = $count;
		}


		foreach( $this->config['monitor'] as $singleMonitorName=>$singleMonitorConfig ){
			if( substr($singleMonitorName,0,6) != 'custom')
				continue;
			$target = $singleMonitorConfig['name'];
			$cmd = $singleMonitorConfig['cmd'];
			$count = exec("cat $fileName | grep $ciTime | $cmd ");
			$result[ $target ] = $count;
		}
		
		return $result;
	}
};
?>