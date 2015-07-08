<?php
	function log_message($level,$message){
		echo "[".strtoupper($level)." ".date('Y-m-d H:i:s')."] => ".$message."\n";
	}
?>