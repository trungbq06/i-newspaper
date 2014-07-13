<?php
define ('DS', DIRECTORY_SEPARATOR);
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
        // $crawler->getKenh14();
		// $crawler->getVnexpress();
		// $crawler->getExchange();
		// $crawler->getGold();
		// $crawler->getOil();
		// $crawler->getWeather();
		// $crawler->getLottery();
		// $crawler->getDantri();
		// $crawler->getVOAEnglish();
		// $crawler->getVnEconomy();
		// $crawler->getNgoisao();
		// $crawler->getKhoahoc();
		// $crawler->getDothi();
		// $crawler->getCafeF();
		// $crawler->get2Sao();
		// $crawler->getVietbao();
		// $crawler->getYahoonews();
		// $crawler->getLandCafe();
		// $crawler->getTuvitrondoi();
		// $crawler->getBongda();
		// $crawler->getInfonet();
		// $crawler->getPcWorld();
		// $crawler->getGenK();
		// $crawler->getZingnews();
		// $crawler->get24h();
		// $crawler->getAutonet();
		// $crawler->getITVietPhoto();
		// $crawler->getXKCN();
		// $crawler->getXKCNFull();
		// $crawler->getSanhdieuTimnhanh();
		// $crawler->getFood();
		// $crawler->getDanhnhan();
		// $crawler->fixDanhnhan();
		// $crawler->parseEbooks();
		// $crawler->parseEbooks2();
		// $crawler->fixEbook();
		// $crawler->getSanhdieuTimnhanh();
		// $crawler->getTruyen18();
		// $crawler->fixTruyen18();
		// $crawler->getMonngon();
		$crawler->getGoTech();
		
		// $crawler->getVnexpressVideo();
		// $crawler->getTvCalendar();
		// $crawler->getLottery();
		// $crawler->getCinemaSchedule();
		// $crawler->getTvCalendar();
		// $crawler->getLotteryCity();
		// $crawler->getTvChannel();
		
        // $this->parseEbooks();
		
		// $crawler->getRadioOnline();
		// $crawler->getSachnoi();
		// $crawler->getWallpaper();
		// $crawler->getWallpaper3();
		// $crawler->getWallpaper2();
		// $crawler->getComic();
		// Comic::model()->fixComic();
		// Comic::model()->createDownloadedFile();
		// Comic::model()->getFirstChar();
        // $crawler->fixComic();
		
		/*$memcache = new Memcache; // instantiating memcache extension class
		$memcache->connect("localhost",11211); // try 127.0.0.1 instead of localhost
											   // if it is not working
	 
		echo "Server's version: " . $memcache->getVersion() . "<br />\n";
	 
		// we will create an array which will be stored in cache serialized
		$testArray = array('horse', 'cow', 'pig');
		$tmp       = serialize($testArray);
	 
		$memcache->add("key", $tmp, 30);
	 
		echo "Data from the cache:<br />\n";
		print_r(unserialize($memcache->get("key")));*/
		
		// $sql = "SELECT * FROM bank_atm";
		// $rows = Yii::app()->db->createCommand($sql)->queryAll();
		// foreach ($rows as $row) {
			// $id = $row['id'];
			// $location = Utility::composite2Unicode(trim($row['location']));
			// $sql = "UPDATE bank_atm SET location = '$location' WHERE id = $id";
			// Yii::app()->db->createCommand($sql)->execute();
		// }
        
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