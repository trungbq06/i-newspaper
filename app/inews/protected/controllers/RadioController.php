<?php
Yii::import('application.components.common.*');
class RadioController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	public function actionGetCats() {
		$this->layout = false;
		$cats = RadioCategory::model()->getList();
		
		$data = array(
			'error' => 0,
			'data' => $cats
		);
		
		echo json_encode($data);
	}
	
	public function actionCat() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : null;
		
		if ($id) {
			$data = array(
				'error' => 0,
				'data' => null
			);
			$rows = Radio::model()->getRadioByCat($id);
			$data['data'] = $rows;
			echo json_encode($data);
		}
	}
    
    public function actionSearch() {
        $keyword = isset($_GET['k']) ? strip_tags($_GET['k']) : null;
        
        if (!empty($keyword)) {
            $data = array(
				'error' => 0,
				'data' => null
			);
            $rows = Radio::model()->findAll("title_en LIKE '%:keyword%'", array(':keyword' => $keyword));
            // print_r($rows);
            $data['data'] = $rows;
            
            echo json_encode($data);
        }
    }
    
}