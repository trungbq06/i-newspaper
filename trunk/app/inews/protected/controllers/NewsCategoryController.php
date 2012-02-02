<?php

class NewsCategoryController extends Controller
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
	
	public function actionGetList() {
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		
		$data = array(
			'error'		=> 0,
			'data'	=> array()
		);
		$category = NewsCategory::model()->getList($siteId);
		if (!empty($category))
			$data['data'] = $category;
			
		echo json_encode($data);
	}
	
	public function actionGetChildList() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 1;
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		
		$data = array(
			'error'		=> 0,
			'data'	=> array()
		);
		$category = NewsCategory::model()->getChildList($id, $siteId);
		if (!empty($category)) {
			$tmp = array(
				'id' => $id,
				'name' => 'Tất cả'
			);
			$newCategory[0] = $tmp;
			foreach ($category as $one) $newCategory[] = $one;
			$data['data'] = $newCategory;
		}
			
		echo json_encode($data);
	}
	
	public function actionGetClipCategory() {
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		
		$data = array(
			'error'		=> 0,
			'data'	=> array()
		);
		$category = NewsCategory::model()->getClipCategory();
		if (!empty($category))
			$data['data'] = $category;
			
		echo json_encode($data);
	}

}