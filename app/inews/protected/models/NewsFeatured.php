<?php

/**
 * This is the model class for table "news_featured".
 *
 * The followings are the available columns in table 'news_featured':
 * @property string $news_id
 * @property string $created_time
 * @property string $order
 */
class NewsFeatured extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return NewsFeatured the static model class
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
		return 'news_featured';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('news_id, created_time, order', 'required'),
			array('order', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('news_id, created_time, order', 'safe', 'on'=>'search'),
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
			'news_id' => 'News',
			'created_time' => 'Created Time',
			'order' => 'Order',
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

		$criteria->compare('news_id',$this->news_id,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('order',$this->order,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}