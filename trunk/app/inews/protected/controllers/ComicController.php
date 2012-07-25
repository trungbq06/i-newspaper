<?php

class ComicController extends Controller
{
	public function actionGetList() {
		$comics = Comic::model()->getAll();
		
		$data['error'] = 0;
		$data['data'] = $comics['data'];
		
		echo json_encode($data);
	}
	
	public function actionGetChapter() {
		$cId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$chapters = Comic::model()->getChapter($cId);
		$data['error'] = 0;
		$data['data'] = $chapters;
		
		echo json_encode($data);
	}
	
	public function actionGetImage() {
		$cId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$images = Comic::model()->getImage($cId);
		$data['error'] = 0;
		$data['data'] = $images;
		
		echo json_encode($data);
	}

	public function actionDetail() {
		$cId = isset($_GET['id']) ? intval($_GET['id']) : null;
		$data = array(
			'error'		=> 0,
			'data'		=> array()
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
					'original_url'		=> $clip->original_url,
					'streaming_url'		=> $clip->streaming_url
				);
			}
		}
        // echo $clip->content;die();
		// var_dump($data);die();
		echo json_encode($data);
	}
	
	public function actionGetById() {
		$ids = isset($_GET['id']) ? $_GET['id'] : null;
		
		if (!empty($ids)) {
			$comics = Comic::model()->getById($ids);
			$data['error'] = 0;
			$data['data'] = $comics['data'];
			
			echo json_encode($data);
		}
	}
	
	public function actionSearch() {
		$keyword = isset($_GET['k']) ? strip_tags($_GET['k']) : null;
		
		if (!empty($keyword)) {
			$comics = Comic::model()->searchComic($keyword);
			$data['error'] = 0;
			$data['data'] = $comics['data'];
			
			echo json_encode($data);
		}
	}
	
}