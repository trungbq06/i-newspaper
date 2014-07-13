<?php

/**
 * This is the model class for table "quiz".
 *
 * The followings are the available columns in table 'quiz':
 * @property string $id
 * @property string $title
 * @property string $result
 * @property string $source
 * @property integer $type
 * @property integer $level
 * @property string $sort
 * @property string $country
 * @property string $genre
 * @property string $created_time
 * @property string $updated_time
 */
class Quiz extends CActiveRecord
{	
	private static $dbadvert = null;
 
    protected static function getAdvertDbConnection()
    {
        if (self::$dbadvert !== null)
            return self::$dbadvert;
        else
        {
            self::$dbadvert = Yii::app()->topdb;
            if (self::$dbadvert instanceof CDbConnection)
            {
                self::$dbadvert->setActive(true);
                return self::$dbadvert;
            }
            else
                throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
        }
    }
	
	public function getDbConnection()
    {
        return self::getAdvertDbConnection();
    }
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Quiz the static model class
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
		return 'quiz';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, result, source, type, level, sort, country, genre, created_time, updated_time', 'required'),
			array('type, level', 'numerical', 'integerOnly'=>true),
			array('title, result', 'length', 'max'=>255),
			array('sort', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, result, source, type, level, sort, created_time, updated_time', 'safe', 'on'=>'search'),
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
			'result' => 'Result',
			'source' => 'Source',
			'type' => 'Type',
			'level' => 'Level',
			'sort' => 'Order',
			'created_time' => 'Created Time',
			'updated_time' => 'Updated Time',
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
		$criteria->compare('result',$this->result,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('level',$this->level);
		$criteria->compare('sort',$this->sort,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('updated_time',$this->updated_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}