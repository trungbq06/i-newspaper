<?php

/**
 * This is the model class for table "clip".
 *
 * The followings are the available columns in table 'clip':
 * @property string $id
 * @property string $title
 * @property string $title_en
 * @property string $headline
 * @property string $headline_en
 * @property string $content
 * @property string $thumbnail_url
 * @property string $category_id
 * @property string $site_id
 * @property string $published_time
 * @property string $created_time
 * @property string $original_url
 * @property integer $youtube_video
 * @property string $streaming_url
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
			array('title, title_en, thumbnail_url, category_id, content, site_id, published_time, created_time, original_url, youtube_video, streaming_url', 'required'),
			array('youtube_video', 'numerical', 'integerOnly'=>true),
			array('title, title_en, headline, headline_en, thumbnail_url, original_url, streaming_url', 'length', 'max'=>255),
			array('category_id, site_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, title_en, headline, content, headline_en, thumbnail_url, category_id, site_id, published_time, created_time, original_url, youtube_video, streaming_url', 'safe', 'on'=>'search'),
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
			'thumbnail_url' => 'Thumbnail Url',
			'category_id' => 'Category',
			'site_id' => 'Site',
			'published_time' => 'Published Time',
			'created_time' => 'Created Time',
			'original_url' => 'Original Url',
			'youtube_video' => 'Youtube Video',
			'streaming_url' => 'Streaming Url',
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
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('site_id',$this->site_id,true);
		$criteria->compare('published_time',$this->published_time,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('original_url',$this->original_url,true);
		$criteria->compare('youtube_video',$this->youtube_video);
		$criteria->compare('streaming_url',$this->streaming_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function isExist($clipUrl) {
		$criteria=new CDbCriteria;
		$criteria->select = 'id';
		$criteria->condition = "original_url = '$clipUrl'";
		
		$clip = Clip::model()->findAll($criteria);
		// var_dump($news);die();
		
		return !empty($clip) ? true : false;
	}
	
	public function getVideoCat($categoryId, $page = 1, $limit = 20) {
		$params = Yii::app()->params;
		
		$offset = ($page - 1) * $limit;
		$query = Yii::app()->db->createCommand()
			->select('c.*')
			->from('clip c')
			->where('c.category_id = ' . $categoryId)
			->order('c.published_time DESC, c.created_time ASC')
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
	
	public function searchText($keyword, $page = 1, $limit = 20) {
		$offset = ($page - 1) * $limit;
        $query = Yii::app()->db->createCommand()
            ->select("id, title, headline, content, thumbnail_url, category_id, published_time, created_time, MATCH(title_en) AGAINST ('$keyword') AS score")
            ->from('clip')
            ->where("MATCH(title_en) AGAINST('$keyword')")
            ->order("score DESC")
            ->limit($limit)
            ->offset($offset);
        $clip = $query->queryAll();
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
            ->from('clip')
            ->where("MATCH(title_en) AGAINST('$keyword')")
            ->queryScalar();
        
        return array('data' => $clip, 'total' => $count);
    }
}