<?php

class UtilityController extends Controller {
	
	public function actionGetCinema() {
		$data = UtilityCinemaSchedule::model()->findByPk(date('Y-m-d'));
		$content = $data->content;
		$content = str_replace('border="1"', '', $content);
		$style = '<style type="text/css">
table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
table td {border: 1px solid #E3DEE0;}
</style>
		';
		$content = str_replace('<table', '<table cellpadding=0', $content);
		$content = $style . $content;
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
				$style = '<style type="text/css">
		table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
		table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
		table td {border: 1px solid #E3DEE0;}
		</style>
				';
				$content = str_replace('<table', '<table cellpadding=0', $content);
				$content = $style . $content;
				$result = array(
					'data' => $content
				);
			} else $result = array('data' => null);
			// echo $content;die();
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
			$style = '<style type="text/css">
	table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
	table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
	table td {border: 1px solid #E3DEE0;}
	</style>
			';
			$content = str_replace('<table', '<table cellpadding=0', $content);
			$content = $style . $content;
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
		$style = '<style type="text/css">
table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
table td {border: 1px solid #E3DEE0;}
</style>
		';
		$content = str_replace('<table', '<table cellpadding=0', $content);
		$content = $style . $content;
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
		$style = '<style type="text/css">
table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
table td {border: 1px solid #E3DEE0;}
</style>
		';
		$content = str_replace('<table', '<table cellpadding=0', $content);
		$content = $style . $content;
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
		$style = '<style type="text/css">
table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
table td {border: 1px solid #E3DEE0;}
</style>
		';
		$content = str_replace('<table', '<table cellpadding=0', $content);
		$content = $style . $content;
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
		$style = '<style type="text/css">
table {border-collapse: collapse;font-size: 14px;border: 1px solid #E3DEE0;border-radius: 5px;padding: 0;}
table thead td {background-color: #BD284A;color: #fff;padding: 2px 0;font-weight: bold;}
table td {border: 1px solid #E3DEE0;}
</style>
		';
		$content = str_replace('<table', '<table cellpadding=0', $content);
		$content = $style . $content;
		$result = array(
			'data' => $content
		);
		// echo $content;die();
		// echo $data->content;die();
		echo json_encode($result);
	}
	
}