<?php

/**
 * This is the model class for table "audio_book".
 *
 * The followings are the available columns in table 'audio_book':
 * @property string $id
 * @property string $title
 * @property string $title_en
 * @property string $headline
 * @property string $headline_en
 * @property string $thumbnail_url
 * @property string $created_time
 * @property string $original_url
 * @property string $media_url
 * @property string $category_id
 * @property string $download_file
 */
class AudioBook extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AudioBook the static model class
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
		return 'audio_book';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, title_en, headline, headline_en, thumbnail_url, created_time, original_url, media_url, category_id, download_file', 'required'),
			array('title, title_en, thumbnail_url, original_url, media_url, download_file', 'length', 'max'=>255),
			array('headline, headline_en', 'length', 'max'=>500),
			array('category_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, title_en, headline, headline_en, thumbnail_url, created_time, original_url, media_url, category_id, download_file', 'safe', 'on'=>'search'),
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
			'thumbnail_url' => 'Thumbnail Url',
			'created_time' => 'Created Time',
			'original_url' => 'Original Url',
			'media_url' => 'Media Url',
			'category_id' => 'Category',
			'download_file' => 'Download File',
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
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('original_url',$this->original_url,true);
		$criteria->compare('media_url',$this->media_url,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('download_file',$this->download_file,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    
    public static function isExist($url) {
		$criteria=new CDbCriteria;
		$criteria->select = 'id';
		$criteria->condition = "original_url = '$url'";
		
		$news = AudioBook::model()->find($criteria);
		// var_dump($news);die();
		
		return !empty($news) ? true : false;
	}
    
}