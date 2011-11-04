<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			// 'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),/**/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=ilive.vn;dbname=inews',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'ilive123312',
			'charset' => 'utf8',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'limit'		=> 5,
		'site' => array(
			'vnexpress' => array(
				1		=> 'http://vnexpress.net/rss/gl/trang-chu.rss',
				2		=> 'http://vnexpress.net/rss/gl/xa-hoi.rss',
				3		=> 'http://vnexpress.net/rss/gl/the-gioi.rss',
				4		=> 'http://vnexpress.net/rss/gl/kinh-doanh.rss',
				5		=> 'http://vnexpress.net/rss/gl/van-hoa.rss',
				6		=> 'http://vnexpress.net/rss/gl/the-thao.rss',
				7		=> 'http://vnexpress.net/rss/gl/phap-luat.rss',
				8		=> 'http://vnexpress.net/rss/gl/doi-song.rss',
				9		=> 'http://vnexpress.net/rss/gl/khoa-hoc.rss',
				10		=> 'http://vnexpress.net/rss/gl/vi-tinh.rss',
				11		=> 'http://vnexpress.net/rss/gl/oto-xe-may.rss',
				12		=> 'http://vnexpress.net/rss/gl/ban-doc-viet.rss',
				13		=> 'http://vnexpress.net/rss/gl/ban-doc-viet-tam-su.rss',
				14		=> 'http://vnexpress.net/rss/gl/cuoi.rss'
			),
			'dantri'	=> array(
				1		=> 'http://dantri.com.vn/trangchu.rss',
				2		=> 'http://dantri.com.vn/xa-hoi.rss',
				3		=> 'http://dantri.com.vn/Thegioi.rss',
				6		=> 'http://dantri.com.vn/The-Thao.rss',
				16		=> 'http://dantri.com.vn/giaoduc-khuyenhoc.rss',
				17		=> 'http://dantri.com.vn/tamlongnhanai.rss',
				4		=> 'http://dantri.com.vn/kinhdoanh.rss',
				18		=> 'http://dantri.com.vn/giaitri.rss',
				19		=> 'http://dantri.com.vn/skphapluat.rss',
				20		=> 'http://dantri.com.vn/nhipsongtre.rss',
				21		=> 'http://dantri.com.vn/tinhyeu-gioitinh.rss',
				22		=> 'http://dantri.com.vn/suckhoe.rss',
				23		=> 'http://dantri.com.vn/cong-nghe.rss',
				24		=> 'http://dantri.com.vn/otoxemay.rss',
				30		=> 'http://dantri.com.vn/diendan-bandoc.rss',
				25		=> 'http://dantri.com.vn/dien-dan.rss',
				26		=> 'http://dantri.com.vn/chuyenla.rss',
				27		=> 'http://dantri.com.vn/blog.rss',
				28		=> 'http://dantri.com.vn/nghe-nghiep.rss',
				29		=> 'http://dantri.com.vn/games_asp_.rss',
			)
		)
	),
);