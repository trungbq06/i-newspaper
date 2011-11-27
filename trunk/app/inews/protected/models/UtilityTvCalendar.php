<?php

/**
 * This is the model class for table "utility_tv_calendar".
 *
 * The followings are the available columns in table 'utility_tv_calendar':
 * @property string $id
 * @property string $created_day
 * @property string $channel_id
 * @property string $content
 */
class UtilityTvCalendar extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UtilityCalendarTv the static model class
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
		return 'utility_tv_calendar';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_day, channel_id, content', 'required'),
			array('channel_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, created_day, channel_id, content', 'safe', 'on'=>'search'),
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
			'created_day' => 'Created Day',
			'channel_id' => 'Channel',
			'content' => 'Content',
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
		$criteria->compare('created_day',$this->created_day,true);
		$criteria->compare('channel_id',$this->channel_id,true);
		$criteria->compare('content',$this->content,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function isExist($channelId, $day) {
		$data = UtilityTvCalendar::model()->findByAttributes(array('channel_id' => $channelId, 'created_day' => $day));
		
		return (!empty($data)) ? true : false;
	}
}