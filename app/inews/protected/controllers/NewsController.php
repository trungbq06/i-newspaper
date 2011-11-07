<?php

class NewsController extends Controller
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
	
	public function actionGetLatest()
	{
		$params = Yii::app()->params;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		$news = News::model()->getLatest($siteId, $page, $limit);
		$data = array(
			'error'		=> 0,
			'news' 		=> null,
            'total'     => 0
		);
		if (!empty($news)) {
			$data['news'] = $news['data'];
			$data['totalPage'] = ceil($news['total']/$limit);
        }
		$this->layout = false;
		
		echo json_encode($data);
	}
	
	public function actionGetFeatured()
	{
		$params = Yii::app()->params;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		
		$news = News::model()->getFeatured($siteId, $page, $limit);
		$data = array(
			'error'		=> 0,
			'news' 		=> null,
            'total'     => 0
		);
		if (!empty($news)) {
			$data['news'] = $news['data'];
            $data['totalPage'] = ceil($news['total']/$limit);
        }
		$this->layout = false;
		
		echo json_encode($data);
	}
    
    public function actionCat() {
        $this->layout = false;
        $params = Yii::app()->params;
        $cId = isset($_GET['id']) ? $_GET['id'] : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $data = array(
            'error'     => 0,
            'news'      => null,
            'total'     => 0
        );        
        $news = array();
        if ($cId) {
            $news = News::model()->getVideoCat($cId, $page, $limit);
        }
        
        if (!empty($news)) {
			$data['news'] = $news['data'];
            $data['totalPage'] = ceil($news['total']/$limit);
        }
		
		echo json_encode($data);
    }
	
	public function actionDetail() {
		$nId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$data = array(
			'error'		=> 0,
			'news'		=> array(
				
			)
		);
		if ($nId) {
			$news = News::model()->findByPk($nId);
			// var_dump($news);
			if ($news) {
				$data['news'][] = array(
					'id'				=> $news->id,
					'title'				=> $news->title,
					'thumbnail_url'		=> $news->thumbnail_url,
					'headline'			=> $news->headline,
					'created_time'		=> date('d/m/Y H:i:s', strtotime($news->created_time)),
					'published_time'	=> date('d/m/Y H:i:s', strtotime($news->published_time)),
					'content'			=> $news->content,
					'category_id'		=> $news->category_id,
				);
			}
		}
        // echo $news->content;die();
		// var_dump($data);die();
		echo json_encode($data);
	}
    
	public function actionNext() {
		$nId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$data = array(
			'error'		=> 0,
			'news'		=> array(
				
			)
		);
		if ($nId) {
			$news = News::model()->findByPk($nId);
			// var_dump($news);
			if ($news) {
				$data['news'] = array(
					'id'				=> $news->id,
					'title'				=> $news->title,
					'thumbnail_url'		=> $news->thumbnail_url,
					'headline'			=> $news->headline,
					'created_time'		=> date('d/m/Y H:i:s', strtotime($news->created_time)),
					'published_time'	=> date('d/m/Y H:i:s', strtotime($news->published_time)),
					'content'			=> $news->content,
					'category_id'		=> $news->category_id,
				);
			}
		}
		
		echo json_encode($data);
	}
    
    public function actionSearch() {
        $params = Yii::app()->params;
        $keyword = isset($_GET['k']) ? strip_tags($_GET['k']) : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        
        $data = array(
            'error'     => 0,
            'news'      => null,
            'total'     => 0
        );
        $news = array();
        if ($keyword) {
            $news = News::model()->searchText($keyword);            
        }
        
        if (!empty($news)) {
			$data['news'] = $news['data'];
            $data['totalPage'] = ceil($news['total']/$limit);
        }
        // var_dump($data);
        echo json_encode($data);
    }

}