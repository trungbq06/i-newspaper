<?php

/**
 * This is the model class for table "xkcn".
 *
 * The followings are the available columns in table 'xkcn':
 * @property string $id
 * @property string $title
 * @property string $thumbnail_url
 * @property string $created_time
 * @property string $width
 * @property string $height
 */
class Xkcn extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Xkcn the static model class
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
		return 'xkcn';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, thumbnail_url, created_time, width, height', 'required'),
			array('title, thumbnail_url', 'length', 'max'=>255),
			array('width, height', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, thumbnail_url, created_time, width, height', 'safe', 'on'=>'search'),
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
			'thumbnail_url' => 'Thumbnail Url',
			'created_time' => 'Created Time',
			'width' => 'Width',
			'height' => 'Height',
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
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('width',$this->width,true);
		$criteria->compare('height',$this->height,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getAll() {
		$data = Yii::app()->db->createCommand("SELECT * FROM xkcn ORDER BY created_time DESC")->queryAll();
		
		return $data;
	}
	
}