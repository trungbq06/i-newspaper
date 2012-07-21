<?php

/**
 * This is the model class for table "chapter".
 *
 * The followings are the available columns in table 'chapter':
 * @property integer $id
 * @property integer $book_id
 * @property string $title
 * @property string $content
 * @property integer $page
 * @property string $heading
 */
class Chapter extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Chapter the static model class
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
		return 'chapter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_id, page', 'numerical', 'integerOnly'=>true),
			array('title, heading', 'length', 'max'=>255),
			array('content', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, book_id, title, content, page, heading', 'safe', 'on'=>'search'),
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
			'book_id' => 'Book',
			'title' => 'Title',
			'content' => 'Content',
			'page' => 'Page',
			'heading' => 'Heading',
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
		$criteria->compare('book_id',$this->book_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('page',$this->page);
		$criteria->compare('heading',$this->heading,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}