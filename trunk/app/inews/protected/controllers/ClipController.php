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
	
	public function actionDetail() {
		$cId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$data = array(
			'error'		=> 0,
			'data'		=> array(
				
			)
		);
		if ($cId) {
			$clip = Clip::model()->findByPk($cId);
			// var_dump($news);
			if ($clip) {
				$data['data'][] = array(
					'id'				=> $clip->id,
					'title'				=> $clip->title,
					'thumbnail_url'		=> $clip->thumbnail_url,
					'headline'			=> $clip->headline,
					'created_time'		=> date('d/m/Y H:i:s', strtotime($clip->created_time)),
					'published_time'	=> date('d/m/Y', strtotime($clip->published_time)),
					'content'			=> $clip->content,
					'category_id'		=> $clip->category_id,
					'original_url'		=> $clip->original_url
				);
			}
		}
        // echo $clip->content;die();
		// var_dump($data);die();
		echo json_encode($data);
	}
	
	public function actionSearch() {
        $params = Yii::app()->params;
        $keyword = isset($_GET['k']) ? strip_tags($_GET['k']) : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        
        $data = array(
            'error'     => 0,
            'data'      => null,
            'total'     => 0
        );
        $clip = array();
        if ($keyword) {
            $clip = Clip::model()->searchText($keyword);            
        }
        
        if (!empty($clip)) {
			$data['data'] = $clip['data'];
            $data['totalPage'] = ceil($clip['total']/$limit);
        }
        // var_dump($data);
        echo json_encode($data);
    }
	
}