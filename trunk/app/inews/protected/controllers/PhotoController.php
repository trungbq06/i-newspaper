<?php
Yii::import('application.components.common.*');
class PhotoController extends Controller
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
    
    public function actiongetList() {
        $this->layout = false;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $data = array(
            'error'     => 0,
            'data'      => null,
            'total'     => 0
        );
		$news = Xkcn::model()->getAll();
        if (!empty($news)) {
			$data['data'] = $news;
			$data['total'] = count($news);
        }
		
		echo json_encode($data);
    }
	
}