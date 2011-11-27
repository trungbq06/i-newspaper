<?php

class UtilityController extends Controller {
	
	public function actionGetCinema() {
		$data = UtilityCinemaSchedule::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionGetLotteryCity() {
		$data = UtilityLotteryCity::model()->findAll();
		foreach ($data as $one) {
			$result[] = array(
				'id' => $one->id,
				'name' => $one->name
			);
		}
		
		echo json_encode(array('data' => $result));
	}
	
	public function actionGetLottery() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : null;
		if ($id) {
			$data = UtilityLottery::model()->getResult($id);

			if (!empty($data)) {
				$result[] = array(
					'data' => 'Kết quả ngày ' . date('d/m/Y', strtotime($data['created_day'])) . '<br/>' . $data['content']
				);
			} else $result[] = array('data' => null);
			echo json_encode($result);
		}
	}
	
	public function actionGetTvChannel() {
		$data = UtilityTvChannel::model()->findAll();
		foreach ($data as $one) {
			$result[] = array(
				'id' => $one->id,
				'name' => $one->name
			);
		}
		echo json_encode(array('data' => $result));
	}
	
	public function actionGetTvCalendar() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : null;
		if ($id) {
			$data = UtilityTvCalendar::model()->findByAttributes(array('created_day' => date('Y-m-d'), 'channel_id' => $id));
			
			$result[] = array(
				'data' => $data->content
			);
			
			echo json_encode($result);
		}
	}
	
	public function actionGetExchange() {
		$data = UtilityExchange::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionGetGold() {
		$data = UtilityGold::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionGetOil() {
		$data = UtilityOil::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
	public function actionGetWeather() {
		$data = UtilityWeather::model()->findByPk(date('Y-m-d'));
		$result[] = array(
			'data' => $data->content
		);
		echo json_encode($result);
	}
	
}