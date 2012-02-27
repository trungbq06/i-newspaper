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
			array('id, name, parent_id, active', 'safe', 'on'=>'search'),
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
			'parent_id' => 'Parent Id'
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
		$criteria->compare('parent_id',$this->parent_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getList($siteId) {
		$data = Yii::app()->db->createCommand()
			->select('nc.*')
			->from('news_category nc')
			->leftJoin('site_category sc', 'nc.id = sc.news_category_id')
			->where('nc.parent_id=0 AND nc.active = 1 AND sc.site_id = ' . $siteId)
			->queryAll();
		foreach ($data as &$one) {
			$one['has_sub_cat'] = 0;
			$tmp = Yii::app()->db->createCommand("SELECT COUNT(id) AS total 
					FROM news_category nc
					INNER JOIN site_category sc ON sc.news_category_id = nc.id
					WHERE sc.site_id = $siteId AND nc.parent_id = " . $one['id'])->queryAll();
			if ($tmp[0]['total'] > 0) $one['has_sub_cat'] = 1;
		}
		return $data;
	}
	
	public function getChildList($id, $siteId) {
		$data = Yii::app()->db->createCommand()
			->select('nc.*')
			->from('news_category nc')
			->leftJoin('site_category sc', 'nc.id = sc.news_category_id')
			->where('nc.active = 1 AND sc.site_id = ' . $siteId . ' AND nc.parent_id = ' . $id)
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