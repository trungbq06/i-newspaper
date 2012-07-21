<?php

/**
 * This is the model class for table "food".
 *
 * The followings are the available columns in table 'food':
 * @property integer $id
 * @property string $title
 * @property string $title_vn
 * @property integer $category_id
 * @property string $created_time
 * @property string $content
 * @property string $thumbnail
 * @property string $thumbnail_url
 * @property string $original_url
 * @property string $headline
 */
class Food extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Food the static model class
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
		return 'food';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id', 'numerical', 'integerOnly'=>true),
			array('title, title_vn, original_url, headline', 'length', 'max'=>255),
			array('created_time, content, thumbnail', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, title_vn, category_id, thumbnail_url, headline, created_time, content, thumbnail, original_url', 'safe', 'on'=>'search'),
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
			'title_vn' => 'Title Vn',
			'category_id' => 'Category',
			'created_time' => 'Created Time',
			'content' => 'Content',
			'thumbnail' => 'Thumbnail',
			'original_url' => 'Original Url',
			'headline' => 'Headline',
			'thumbnail_url' => 'thumbnail_url',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('title_vn',$this->title_vn,true);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('thumbnail',$this->thumbnail,true);
		$criteria->compare('original_url',$this->original_url,true);
		$criteria->compare('headline',$this->headline,true);
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function isExist($articleUrl) {
		$criteria=new CDbCriteria;
		$criteria->select = 'id';
		$criteria->condition = "original_url = '$articleUrl'";
		
		$news = Food::model()->findAll($criteria);
		
		return !empty($news) ? true : false;
	}
}