<?php

/**
 * This is the model class for table "news_category".
 *
 * The followings are the available columns in table 'news_category':
 * @property string $id
 * @property string $name
 * @property integer $active
 */
class NewsCategory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return NewsCategory the static model class
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
		return 'news_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('active', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, active', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'active' => 'Active',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getList($siteId) {
		$data = Yii::app()->db->createCommand()
			->select('nc.*')
			->from('news_category nc')
			->leftJoin('site_category sc', 'nc.id = sc.news_category_id')
			->where('sc.site_id = ' . $siteId)
			->queryAll();
			
		return $data;
	}
	
	public function getClipCategory() {
		$data = Yii::app()->db->createCommand()
			->select('cc.*')
			->from('clip_category cc')
			->queryAll();
		
		return $data;
	}
}