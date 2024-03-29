<?php

/**
 * This is the model class for table "handbook_eva".
 *
 * The followings are the available columns in table 'handbook_eva':
 * @property string $id
 * @property string $title
 * @property string $headline
 * @property string $content
 * @property string $created_time
 * @property string $published_time
 * @property string $thumbnail_url
 * @property string $original_url
 */
class HandbookEva extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HandbookEva the static model class
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
		return 'handbook_eva';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, headline, content, created_time, published_time, thumbnail_url, original_url', 'required'),
			array('title, headline, thumbnail_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, headline, content, created_time, published_time, original_url, thumbnail_url', 'safe', 'on'=>'search'),
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
			'headline' => 'Headline',
			'content' => 'Content',
			'created_time' => 'Created Time',
			'published_time' => 'Published Time',
			'thumbnail_url' => 'Thumbnail Url',
			'original_url' => 'Original Url',
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
		$criteria->compare('headline',$this->headline,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('published_time',$this->published_time,true);
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('original_url',$this->original_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    
    public static function isExist($articleUrl) {
		$criteria=new CDbCriteria;
		$criteria->select = 'id';
		$criteria->condition = "original_url = '$articleUrl'";
		
		$news = HandbookEva::model()->findAll($criteria);
		
		return !empty($news) ? true : false;
	}
}