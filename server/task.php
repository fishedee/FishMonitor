<?php
	require_once(dirname(__FILE__).'/monitor/monitorTask.php');
	require_once(dirname(__FILE__).'/service/serviceTask.php');
	use Workerman\Worker;
	class Task{
		var $user;
		var $logPath;
		var $port;

		public function __construct($config){
			//配置端口
			$networkConfig = $config->getNetworkConfig();
			if( isset($networkConfig['port']) )
				$this->port = $networkConfig['port'];
			else
				$this->port = 2346;

			//配置user
			$this->user = $config->getUser();

			//配置日志
			$logConfig = $config->getLogConfig();
			if( isset($logConfig['path']))
				$this->logPath = $logConfig['path'];
			else
				$this->logPath = '';

			//配置任务
			if( $config->isStart() ){
				$this->initMonitorTask($config);
				$this->initServiceTask($config);
			}
		}

		private function initMonitorTask($config){
			//配置监控器任务
			$task = new MonitorTask($config);

			$worker = new Worker("http://0.0.0.0:".$this->port);

			if( $this->user != '')
				$worker->user = $this->user;

			if( $this->logPath != '')
				$worker->stdoutFile = $this->logPath;

			$worker->count = 1;

			$worker->onWorkerStart = function()use($task){
				$task->onWorkerStart();
			};

			$worker->onMessage = function($connection,$data)use($task){
				$task->onMessage($connection,$data);
			};
		}

		private function initServiceTask($config){
			//配置服务任务
			$serviceConfig = $config->getServiceConfig();

			foreach( $serviceConfig as $serviceName=>$singleServiceConfig ){
				$task = new ServiceTask($serviceName,$singleServiceConfig,$this->port);

				$worker = new Worker();

				if( $this->logPath != '')
					$worker->stdoutFile = $this->logPath;

				$worker->count = 1;

				$worker->onWorkerStart = function()use($task){
					$task->onWorkerStart();
				};

				$worker->onMessage = function($connection,$data)use($task){
					$task->onMessage($connection,$data);
				};
			}
				
		}

		public function run(){
			Worker::runAll();
		}
	};
?>