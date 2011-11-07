<?php

/**
 * This is the model class for table "clip".
 *
 * The followings are the available columns in table 'clip':
 * @property string $id
 * @property string $title
 * @property string $title_en
 * @property string $content
 * @property string $content_en
 * @property string $thumbnail_url
 * @property string $category_id
 * @property string $site_id
 * @property string $published_time
 * @property string $created_time
 * @property string $original_url
 * @property integer $youtube_video
 */
class Clip extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Clip the static model class
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
		return 'clip';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, title_en, content, content_en, thumbnail_url, category_id, site_id, published_time, created_time, original_url, youtube_video', 'required'),
			array('youtube_video', 'numerical', 'integerOnly'=>true),
			array('title, title_en, content, content_en, thumbnail_url, original_url', 'length', 'max'=>255),
			array('category_id, site_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, title_en, content, content_en, thumbnail_url, category_id, site_id, published_time, created_time, original_url, youtube_video', 'safe', 'on'=>'search'),
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
			'content' => 'Content',
			'content_en' => 'Content En',
			'thumbnail_url' => 'Thumbnail Url',
			'category_id' => 'Category',
			'site_id' => 'Site',
			'published_time' => 'Published Time',
			'created_time' => 'Created Time',
			'original_url' => 'Original Url',
			'youtube_video' => 'Youtube Video',
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('content_en',$this->content_en,true);
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('site_id',$this->site_id,true);
		$criteria->compare('published_time',$this->published_time,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('original_url',$this->original_url,true);
		$criteria->compare('youtube_video',$this->youtube_video);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getVideoCat($categoryId, $page = 1, $limit = 20) {
		$params = Yii::app()->params;
		
		$offset = ($page - 1) * $limit;
		$query = Yii::app()->db->createCommand()
			->select('c.*')
			->from('clip c')
			->where('c.category_id = ' . $categoryId)
			->order('c.created_time DESC')
			->limit($limit)
			->offset($offset);
		$news = $query->queryAll();
		// var_dump($news);
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(c.id)')
			->from('clip c')
			->where('c.category_id = ' . $categoryId)
            ->queryScalar();
		
		return array('data' => $news, 'total' => $count);
	}
}