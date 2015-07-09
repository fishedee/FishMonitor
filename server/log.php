<?php
	use Monolog\Logger;
	use Monolog\Handler\StreamHandler;
	use Monolog\Formatter\LineFormatter;
	class Log{
		public static $logger = null;

		public function __construct($config){
			if( self::$logger != null )
				return;

			$logConfig = $config->getLogConfig();
			$logPath = isset($logConfig['path'])?$logConfig['path']:'/tmp/monitor.log';
			$logDay = isset($logConfig['day'])?$logConfig['day']:0;
			$logLevel = isset($logConfig['level'])?$logConfig['level']:'debug';

			$logLevelMap = array(
				'debug'=>Logger::DEBUG,
				'info'=>Logger::INFO,
				'warn'=>Logger::WARNING,
				'error'=>Logger::ERROR
			);
			if( isset($logLevelMap[$logLevel]) == false )
				throw new Exception('日志级别设置错误',1);

			$dateFormat = "Y-m-d H:i:s";
			$output = "[%datetime%] %level_name%: %message% \n";
			$formatter = new LineFormatter($output, $dateFormat);

			$stream = new StreamHandler($logPath, $logDay,$logLevelMap[$logLevel]);
			$stream->setFormatter($formatter);

			self::$logger = new Logger('');
			self::$logger->pushHandler($stream);
		}

		public static function message($level,$message){
			self::$logger->log($level,$message);
		}
	}
	function log_message($level,$message){
		Log::message($level,$message);
	}
?>