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
			'category'	=> array()
		);
		$category = NewsCategory::model()->getList($siteId);
		if (!empty($category))
			$data['category'] = $category;
			
		echo json_encode($data);
	}
	
	public function actionGetClipCategory() {
		$siteId = isset($_GET['sid']) ? intval($_GET['sid']) : 1;
		
		$data = array(
			'error'		=> 0,
			'category'	=> array()
		);
		$category = NewsCategory::model()->getClipCategory();
		if (!empty($category))
			$data['category'] = $category;
			
		echo json_encode($data);
	}

}