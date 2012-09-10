<?php

/**
 * This is the model class for table "wallpaper".
 *
 * The followings are the available columns in table 'wallpaper':
 * @property string $id
 * @property string $title
 * @property string $thumbnail_url
 * @property string $created_time
 * @property string $width_160
 * @property string $height_160
 * @property string $small_thumbnail_url
 * @property string $width_320
 * @property string $height_320
 * @property string $category_id
 */
class Wallpaper extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Wallpaper the static model class
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
		return 'wallpaper';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, thumbnail_url, created_time, small_thumbnail_url', 'required'),
			array('title, thumbnail_url, small_thumbnail_url, category_id', 'length', 'max'=>255),
			array('width_160, height_160, width_320, height_320', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, thumbnail_url, created_time, category_id, width_160, height_160, small_thumbnail_url, width_320, height_320', 'safe', 'on'=>'search'),
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
			'width_160' => 'Width 160',
			'height_160' => 'Height 160',
			'small_thumbnail_url' => 'Small Thumbnail Url',
			'width_320' => 'Width 320',
			'height_320' => 'Height 320',
			'category_id' => 'Height 320',
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
		$criteria->compare('width_160',$this->width_160,true);
		$criteria->compare('height_160',$this->height_160,true);
		$criteria->compare('small_thumbnail_url',$this->small_thumbnail_url,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('width_320',$this->width_320,true);
		$criteria->compare('height_320',$this->height_320,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getAll($page, $limit) {
        $offset = ($page - 1) * $limit;
		$data = Yii::app()->db->createCommand("SELECT * FROM wallpaper ORDER BY created_time DESC LIMIT $offset, $limit")->queryAll();
		
		return $data;
	}
}