<?php
	class Config{
		var $config = array();
		var $mode;

		public function __construct(){
			$this->analyseCommandLine();
			$this->analyseIP();
		}

		//系统
		public function getMachineIP(){
			return $this->config['machineIP'];
		}

		public function getUser(){
			return isset($this->config['user'])?$this->config['user']:'';
		}

		public function isStart(){
			return ($this->mode == 'start' || $this->mode == 'restart');
		}

		//网络
		public function getNetworkConfig(){
			return isset($this->config['network'])?$this->config['network']:array();
		}

		//日志
		public function getLogConfig(){
			return isset($this->config['log'])?$this->config['log']:array();
		}
		
		//监控
		public function getMonitorConfig(){
			return isset($this->config['monitor'])?$this->config['monitor']:array();
		}

		//服务
		public function getServiceConfig(){
			return isset($this->config['monitor']['service'])?$this->config['monitor']['service']:array();
		}

		private function analyseIP(){
			$contents = (new GuzzleHttp\Client())
							->get('http://1111.ip138.com/ic.asp')
							->getBody()
							->getContents();
			preg_match('/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$contents,$matches);
			$this->config['machineIP'] = $matches[0];
		}

		public function analyseCommandLine(){
			global $argv;
			if( isset($argv[1]) == false )
				throw new Exception('php yourfile.php {start|stop|restart|reload|status}',1);
			$this->mode = $argv[1];
			if( $this->isStart() == false )
				return;

			for( $i = 0 ; $i != count($argv) ; $i++ ){
				if( $argv[$i] == '-c' && $i + 1 < count($argv) ){
					$this->config = json_decode(
						file_get_contents($argv[$i+1]),
						true
					);
					if( $this->config == null )
						throw new Exception('配置文件含有语法错误，不是合法的json格式');
					return;
				}
			}
			throw new Exception('请使用-c参数指定配置文件的位置',1);
		}
	}
?>