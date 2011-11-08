<?php

class ClipController extends Controller
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

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		
	}
	
	public function actionCat() {
		$this->layout = false;
        $params = Yii::app()->params;
        $cId = isset($_GET['id']) ? $_GET['id'] : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $data = array(
            'error'     => 0,
            'data'      => null,
            'total'     => 0
        );        
        $clip = array();
        if ($cId) {
            $clip = Clip::model()->getVideoCat($cId, $page, $limit);
        }
        
        if (!empty($clip)) {
			$data['data'] = $clip['data'];
            $data['totalPage'] = ceil($clip['total']/$limit);
        }
		
		echo json_encode($data);
	}
	
}