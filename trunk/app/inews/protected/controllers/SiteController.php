<?php

class SiteController extends Controller
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
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		// echo date('Y-m-d H:i:s', strtotime('Fri, 14 Oct 2011 11:25:36 GMT'));
		/*
		$news = new News;
						
		$news->attributes = array(
			'title' => 'test',
			'headline' => 'test',
			'thumbnail_url' => 'test',
			'published_time' => 'test',
			'content' => 'test',
			'original_url' => 'test',
			'category_id' => 1,
			'created_time' => 'test'			
		);
		if (!$news->save()) echo 'not saved';*/
		// echo urldecode('xmlPath=%2FService%2FFlashVideo%2FPlayListVideoPage.asp%3Fid%3D59398%26f%3D153');die();
        // die(strip_tags('<a class="" href="/gl/van-hoa/2011/03/khan-gia-hn-chim-trong-cam-xuc-cung-backstreet-boys/">Ðêm nh?c Backstreet Boys</a>'));
		$crawler = new Crawler();
		// $crawler->getVnexpress();
		// $crawler->getExchange();
		// $crawler->getGold();
		// $crawler->getOil();
		// $crawler->getWeather();
		// $crawler->getLottery();
		// $crawler->getDantri();
		$crawler->getVOAEnglish();
		
		// $crawler->getVnexpressVideo();
		// $crawler->getTvCalendar();
		// $crawler->getLottery();
		// $crawler->getCinemaSchedule();
		// $crawler->getTvCalendar();
		// $crawler->getLotteryCity();
		// $crawler->getTvChannel();
		
		die('Hello world');
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}