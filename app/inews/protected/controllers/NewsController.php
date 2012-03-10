<?php
Yii::import('application.components.common.*');
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
			'data' 		=> null,
            'total'     => 0
		);
		if (!empty($news)) {
			$data['data'] = $news['data'];
			$data['total'] = $news['total'];
			$data['totalPage'] = ceil($news['total']/$limit);
        }
		$this->layout = false;
		
		echo json_encode($data);
	}
	
	public function actionGetFeatured()
	{
		$params = Yii::app()->params;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$limit = 5;
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		$catId = isset($_GET['cid']) ? intval($_GET['cid']) : 1;
		
		$news = News::model()->getFeatured($siteId, $catId, $page, $limit);
		$data = array(
			'error'		=> 0,
			'data' 		=> null,
            'total'     => 0
		);
		if (!empty($news)) {
			$data['data'] = $news['data'];
			$data['total'] = $news['total'];
            $data['totalPage'] = ceil($news['total']/$limit);
        }
		$this->layout = false;
		
		echo json_encode($data);
	}
    
    public function actionCat() {
        $this->layout = false;
        $params = Yii::app()->params;
        $cId = isset($_GET['id']) ? $_GET['id'] : null;
		$siteId = isset($_GET['sid']) ? strip_tags($_GET['sid']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $data = array(
            'error'     => 0,
            'data'      => null,
            'total'     => 0
        );        
        $news = array();
        if ($cId) {
            $newsFeatured = News::model()->getFeatured($siteId, $cId, 1, 5);
            $excludeId = array();
            if (!empty($newsFeatured['data'])) {
                foreach ($newsFeatured['data'] as $one) {
                    $excludeId[] = $one['id'];
                }
            }
            $news = News::model()->getNewsCat($cId, $siteId, $excludeId, $page, $limit);
        }
        
        if (!empty($news)) {
			$data['data'] = $news['data'];
			$data['total'] = $news['total'];
            $data['totalPage'] = ceil($news['total']/$limit);
        }
		
		echo json_encode($data);
    }
	
	public function actionFixNews() {
		$this->layout = false;
		News::model()->fixNews();
	}
	
	public function actionDetail() {
		$nId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$data = array(
			'error'		=> 0,
			'data'		=> array(
				
			)
		);
		if ($nId) {
			$news = News::model()->findByPk($nId);
			$cat = NewsCategory::model()->findByPk($news->category_id);
			// var_dump($news);
			if ($news) {
				$data['data'][] = array(
					'id'				=> $news->id,
					'title'				=> $news->title,
					'thumbnail_url'		=> $news->thumbnail_url,
					'headline'			=> $news->headline,
					'created_time'		=> date('d/m/Y H:i:s', strtotime($news->created_time)),
					'published_time'	=> date('d/m/Y H:i:s', strtotime($news->published_time)),
					'content'			=> $this->parseVideo($news),
					'category_id'		=> $news->category_id,
					'original_url'		=> $news->original_url,
					'category_name'		=> $cat->name
				);
			}
		}
		// echo urldecode('http://o-o.preferred.fpt-han1.v22.lscache8.c.youtube.com/videoplayback?sparams=id%2Cexpire%2Cip%2Cipbits%2Citag%2Csource%2Cratebypass%2Ccp&fexp=904520%2C916201%2C901100&itag=18&ip=183.0.0.0&signature=306A7373AB4D1A9753A5E32B3087B2B9DA95FABE.077D67CC3FE03F10CFDD54B80ADDC4D0E4EF869D&sver=3&ratebypass=yes&source=youtube&expire=1322231122&key=yt1&ipbits=8&cp=U0hRRVdUUV9FSkNOMV9PTlVDOmFXWjlyR0hSU3lV&id=35494260a24637d6');
		// echo date('Y-m-d H:i:s', 1320793200);
		// echo strtotime('+10 days', time());
		// die();
        echo strip_tags($news->content, '<img>');die();
		// var_dump($data);die();
		// echo ($data['data'][0]['content']);die();
		echo json_encode($data);
	}
	
	public function parseVideo($news) {
		$content = $news->content;
		if ($news->youtube_data != '') {
			$parser = new Videopian();
			$links = json_decode($news->youtube_data);
			// var_dump($links);
			foreach ($links as $key => $link) {
				$videoInfo = $parser->get($link);
				$videoUrl = '';
				$replace = '';
				if ($videoInfo->files['video/mp4'] != '')
					$videoUrl = $videoInfo->files['video/mp4'];
				if ($videoUrl != '') {
					$videoTag = '<video poster="' . $videoInfo->thumbnails[0]->url . '" controls>
					<source src="' . $videoUrl . '" type=\'video/mp4; codecs="avc1.4D401E, mp4a.40.2"\' />
					</video>';
					$replace = $videoTag;
				}
				$content = str_replace($key, $replace, $content);
			}
			
			return $content;
		} else return $content;
	}
    
	public function actionNext() {
		$nId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$data = array(
			'error'		=> 0,
			'data'		=> array(
				
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
        $page = isset($_GET['page']) ? strip_tags($_GET['page']) : 1;
        $keyword = isset($_GET['k']) ? strip_tags($_GET['k']) : null;
        $siteId = isset($_GET['sid']) ? strip_tags($_GET['sid']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $params['limit'];
        
        $data = array(
            'error'     => 0,
            'data'      => null,
            'total'     => 0
        );
        $news = array();
        if ($keyword) {
            $news = News::model()->searchText($keyword, $siteId, $page, $limit);
        }
        
        if (!empty($news)) {
			$data['data'] = $news['data'];
			$data['total'] = $news['total'];
            $data['totalPage'] = ceil($news['total']/$limit);
        }
        // var_dump($data);
        echo json_encode($data);
    }
    
    public function actionGetApp() {
        $apps = News::model()->getApp();
        
        if (!empty($apps)) {
			$data['data'] = $apps;
        }
        // var_dump($data);
        echo json_encode($data);
    }
	
	public function actionLoadUrl() {
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : null;
		$categoryId = isset($_GET['cid']) ? intval($_GET['cid']) : null;
		$url = isset($_GET['url']) ? urldecode(trim($_GET['url'])) : null;
		
		$data = array(
			'error'		=> 0,
			'data'		=> array(
				
			)
		);
		
		if (!News::model()->isExist($siteId, $url)) {
			$function = News::model()->getSite($siteId);
			$function = $function['crawl_function'];
			$crawler = new Crawler();
			$crawler->{$function}($siteId, $categoryId, $url);
		}
		$news = News::model()->getByUrl($siteId, $url);
		$cat = NewsCategory::model()->findByPk($news->category_id);
		
		if ($news) {
			$data['data'][] = array(
				'id'				=> $news->id,
				'title'				=> $news->title,
				'thumbnail_url'		=> $news->thumbnail_url,
				'headline'			=> $news->headline,
				'created_time'		=> date('d/m/Y H:i:s', strtotime($news->created_time)),
				'published_time'	=> date('d/m/Y H:i:s', strtotime($news->published_time)),
				'content'			=> $this->parseVideo($news),
				'category_id'		=> $news->category_id,
				'original_url'		=> $news->original_url,
				'category_name'		=> $cat->name
			);
		}
		echo json_encode($data);
	}

}