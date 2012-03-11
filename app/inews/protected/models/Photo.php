<?php

/**
 * This is the model class for table "photo".
 *
 * The followings are the available columns in table 'photo':
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $thumbnail_url
 * @property string $created_time
 * @property string $original_url
 */
class Photo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Photo the static model class
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
		return 'photo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, description, thumbnail_url, created_time, original_url', 'required'),
			array('title, description, thumbnail_url, original_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, description, thumbnail_url, created_time, original_url', 'safe', 'on'=>'search'),
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
			'description' => 'Description',
			'thumbnail_url' => 'Thumbnail Url',
			'created_time' => 'Created Time',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('original_url',$this->original_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getAllByNewsId($newsId) {
		$data = Yii::app()->db->createCommand("SELECT * FROM photo_detail WHERE photo_id = $newsId ORDER BY created_time DESC")->queryAll();
		
		return $data;
	}
	
	public function getAll() {
		$data = Yii::app()->db->createCommand("SELECT * FROM photo_detail ORDER BY created_time DESC")->queryAll();
		
		return $data;
	}
	
}