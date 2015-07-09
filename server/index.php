<?php
	require_once(dirname(__FILE__).'/../vendor/autoload.php');
	require_once(dirname(__FILE__).'/log.php');
	require_once(dirname(__FILE__).'/config.php');
	require_once(dirname(__FILE__).'/task.php');

	try{
		//启动config
		$config = new Config();

		//启动日志
		$logger = new Log($config);

		//启动Task
		$task = new Task($config);

		//启动worker
		$task->run();
	}catch( Exception $e){
		echo $e->getMessage()."\n";
	}
?>