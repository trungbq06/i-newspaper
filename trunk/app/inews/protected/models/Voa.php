<?php

/**
 * This is the model class for table "voa".
 *
 * The followings are the available columns in table 'voa':
 * @property string $id
 * @property string $title
 * @property string $title_en
 * @property string $headline
 * @property string $headline_en
 * @property string $content
 * @property string $content_en
 * @property string $thumbnail_url
 * @property string $published_time
 * @property string $created_time
 * @property string $original_url
 * @property string $media
 */
class Voa extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Voa the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'voa';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, title_en, headline, headline_en, content, content_en, thumbnail_url, published_time, created_time, original_url, media', 'required'),
			array('title, title_en, headline, headline_en, thumbnail_url, original_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, title_en, headline, headline_en, content, content_en, thumbnail_url, published_time, created_time, original_url, media', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'title_en' => 'Title En',
			'headline' => 'Headline',
			'headline_en' => 'Headline En',
			'content' => 'Content',
			'content_en' => 'Content En',
			'thumbnail_url' => 'Thumbnail Url',
			'published_time' => 'Published Time',
			'created_time' => 'Created Time',
			'original_url' => 'Original Url',
			'media' => 'Media',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('title_en',$this->title_en,true);
		$criteria->compare('headline',$this->headline,true);
		$criteria->compare('headline_en',$this->headline_en,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('content_en',$this->content_en,true);
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('published_time',$this->published_time,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('original_url',$this->original_url,true);
		$criteria->compare('media',$this->media,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function isExist($articleUrl) {
		$articleUrl = trim($articleUrl);
		$criteria=new CDbCriteria;
		$criteria->select = 'id';
		$criteria->condition = "original_url = '$articleUrl'";
		
		$news = News::model()->findAll($criteria);
		// var_dump($news);die();
		
		return !empty($news) ? true : false;
	}
}