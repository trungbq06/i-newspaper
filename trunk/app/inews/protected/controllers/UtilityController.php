<?php

class UtilityController extends Controller {
	
	public function actionExchange() {
		$data = UtilityExchange::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionGold() {
		$data = UtilityGold::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionOil() {
		$data = UtilityOil::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionWeather() {
		$data = UtilityWeather::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
}