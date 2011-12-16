<?php

class UtilityController extends Controller {
	
	public function actionGetCinema() {
		$data = UtilityCinemaSchedule::model()->findByPk(date('Y-m-d'));
		$content = $data->content;
		$content = str_replace('border="1"', '', $content);
		$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
		$result = array(
			'data' => $content
		);
		// echo $content;die();
		// echo $data->content;die();
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
				$result = array(
					'data' => 'Kết quả ngày ' . date('d/m/Y', strtotime($data['created_day'])) . '<br/>' . $data['content']
				);
				$content = $result['data'];
				$content = str_replace('border="1"', '', $content);
				$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
				$result = array(
					'data' => $content
				);
			} else $result = array('data' => null);
			// echo $data['content'];die();			
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
			
			$content = $data->content;
			$content = str_replace('border="1"', '', $content);
			$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
			$result = array(
				'data' => $content
			);
			// echo $content;die();
			// echo $data->content;die();
			echo json_encode($result);
		}
	}
	
	public function actionGetExchange() {
		$data = UtilityExchange::model()->findByPk(date('Y-m-d'));
		$content = $data->content;
		$content = str_replace('border="1"', '', $content);
		$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
		$result = array(
			'data' => $content
		);
		// echo $content;die();
		// echo $data->content;die();
		echo json_encode($result);
	}
	
	public function actionGetGold() {
		$data = UtilityGold::model()->findByPk(date('Y-m-d'));
		$content = $data->content;
		$content = str_replace('border="1"', '', $content);
		$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
		$result = array(
			'data' => $content
		);
		// echo $content;die();
		// echo $data->content;die();
		echo json_encode($result);
	}
	
	public function actionGetOil() {
		$data = UtilityOil::model()->findByPk(date('Y-m-d'));
		$content = $data->content;
		$content = str_replace('border="1"', '', $content);
		$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
		$result = array(
			'data' => $content
		);
		// echo $content;die();
		// echo $data->content;die();
		echo json_encode($result);
	}
	
	public function actionGetWeather() {
		$data = UtilityWeather::model()->findByPk(date('Y-m-d'));
		
		$content = $data->content;
		$content = str_replace('border="1"', '', $content);
		$content = str_replace('<td', '<td style="border: 1px solid #E3DEE0;"', $content);
		$result = array(
			'data' => $content
		);
		// echo $content;die();
		// echo $data->content;die();
		echo json_encode($result);
	}
	
}