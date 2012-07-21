<?php

/**
 * This is the model class for table "comic_image".
 *
 * The followings are the available columns in table 'comic_image':
 * @property string $id
 * @property string $chapter_id
 * @property string $image
 * @property string $created_time
 */
class ComicImage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ComicImage the static model class
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
		return 'comic_image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chapter_id, image, created_time', 'required'),
			array('chapter_id', 'length', 'max'=>10),
			array('image', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, chapter_id, image, created_time', 'safe', 'on'=>'search'),
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
			'chapter_id' => 'Chapter',
			'image' => 'Image',
			'created_time' => 'Created Time',
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
		$criteria->compare('chapter_id',$this->chapter_id,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('created_time',$this->created_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}