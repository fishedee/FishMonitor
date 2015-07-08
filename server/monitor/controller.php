<?php
	require_once(dirname(__FILE__).'/model.php');
	class Controller{

		var $model;

		public function __construct($config){
			$this->model = new Model($config);
		}

		private function checkGet($argvs){
			$result = array();
			foreach( $argvs as $argv ){
				if( isset($_GET[$argv]) == false )
					throw new Exception('缺少参数'.$argv,1);
				$result[$argv] = $_GET[$argv];
			}
			return $result;
		}

		public function add(){
			$data = $this->checkGet(array(
				'id',
				'value'
			));
			$this->model->add(
				$data['id'],
				$data['value']
			);
		}

		public function min(){
			$data = $this->checkGet(array(
				'id',
				'value'
			));
			$this->model->min(
				$data['id'],
				$data['value']
			);
		}

		public function max(){
			$data = $this->checkGet(array(
				'id',
				'value'
			));
			$this->model->max(
				$data['id'],
				$data['value']
			);
		}

		public function set(){
			$data = $this->checkGet(array(
				'id',
				'value'
			));
			$this->model->set(
				$data['id'],
				$data['value']
			);
		}

		public function refresh(){
			$this->model->upload();
		}
	}
?>