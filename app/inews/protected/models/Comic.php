<?php

/**
 * This is the model class for table "comic".
 *
 * The followings are the available columns in table 'comic':
 * @property string $id
 * @property string $title
 * @property string $title_vn
 * @property string $description
 * @property string $created_time
 * @property string $category_id
 * @property string $thumbnail_url
 * @property string $approved
 * @property string $down_thumb
 */
class Comic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Comic the static model class
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
		return 'comic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, title_vn, description, created_time, category_id, thumbnail_url', 'required'),
			array('title, title_vn, description, thumbnail_url', 'length', 'max'=>255),
			array('category_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, title_vn, down_thumb, approved , description, created_time, category_id, thumbnail_url', 'safe', 'on'=>'search'),
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
			'title' => 'Name',
			'title_vn' => 'Name Vn',
			'description' => 'Description',
			'created_time' => 'Created Time',
			'category_id' => 'Category',
			'thumbnail_url' => 'Thumbnail Url',
			'approved' => 'Thumbnail Url',
			'down_thumb' => 'Down Thumb',
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
		$criteria->compare('title_vn',$this->title_vn,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created_time',$this->created_time,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('thumbnail_url',$this->thumbnail_url,true);
		$criteria->compare('approved',$this->approved,true);
		$criteria->compare('down_thumb',$this->down_thumb,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function isExist($title) {
		$title = Yii::app()->db->quoteValue($title);
		$sql = "SELECT id FROM comic WHERE title = $title";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		
		return !empty($row) ? true : false;
	}
	
	public function getById($ids) {
		$query = Yii::app()->db->createCommand()
			->select('c.*')
			->where("approved = 1 AND id IN ($ids)")
			->from('comic c')
			->order('title_vn ASC');
		$comics = $query->queryAll();
		
		foreach ($comics as &$one) {
			$sql = "SELECT COUNT(id) AS total FROM comic_chapter WHERE comic_id = " . $one['id'];
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			$one['total_part'] = $row['total'];
		}
		
		return array('data' => $comics);
	}
	
	public function getAll() {
		$query = Yii::app()->db->createCommand()
			->select('c.id, c.title, c.down_thumb AS thumbnail_url, c.created_time, c.first_char, c.category_id')
			->where('approved = 1')
			->from('comic c')
			->order('first_char ASC');
		$comics = $query->queryAll();
		
		foreach ($comics as &$one) {
			$sql = "SELECT COUNT(id) AS total FROM comic_chapter WHERE comic_id = " . $one['id'];
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			$one['total_part'] = $row['total'];
		}
		
		return array('data' => $comics);
	}
	
	public function getChapter($id) {
		$query = Yii::app()->db->createCommand()
			->select('ch.*')
			->from('comic_chapter ch')
			->where("ch.comic_id = $id");
			
		$chapters = $query->queryAll();
		// print_r($chapters);die();
		// foreach ($chapters as &$one) {
			// $sql = "SELECT COUNT(id) AS total FROM comic_image WHERE chapter_id = " . $one['id'];
			// $row = Yii::app()->db->createCommand($sql)->queryRow();
			// $one['total_part'] = $row['total'];
		// }
		
		return $chapters;
	}
	
	public function getImage($id) {
		$query = Yii::app()->db->createCommand()
			->select('*')
			->from('comic_image ci')
			->where("chapter_id = $id");
			
		$images = $query->queryAll();
		// print_r($images);die();
		$i = 0;
		foreach ($images as &$one) {
			if (empty($one['downloaded_file'])) {
				$i++;
				$pathInfo = pathinfo($one['image']);
				$extension = $pathInfo['extension'];
				$file = date('YmdHis') . '_' . $i . '.' . $extension;
				$one['downloaded_file'] = $file;
				$sql = "UPDATE comic_image SET downloaded_file = '$file' WHERE id = " . $one['id'] . " LIMIT 1";
				// die($sql);
				Yii::app()->db->createCommand($sql)->execute();
			}
		}
		
		return $images;
	}
	
	public function searchComic($keyword, $page = 1, $limit = 1000) {
		$offset = ($page - 1) * $limit;
        $query = Yii::app()->db->createCommand()
            ->select("id, title, thumbnail_url, MATCH(title_vn) AGAINST ('$keyword') AS score")
            ->from('comic')
            ->where("MATCH(title_vn) AGAINST('$keyword') AND approved = 1")
            ->order("score DESC")
            ->limit($limit)
            ->offset($offset);
        $comics = $query->queryAll();
		
		foreach ($comics as &$one) {
			$sql = "SELECT COUNT(id) AS total FROM comic_chapter WHERE comic_id = " . $one['id'];
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			$one['total_part'] = $row['total'];
		}
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
            ->from('comic')
            ->where("MATCH(title_vn) AGAINST('$keyword')")
            ->queryScalar();
        
        return array('data' => $comics, 'total' => $count);
	}
	
	public function fixComic() {
		$sql = "SELECT DISTINCT(chapter_id) FROM comic_image ORDER BY id";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$chapterId = array();
		foreach ($rows as $one) {
			$chapterId[] = $one['chapter_id'];
		}
		$chapterStr = implode(',', $chapterId);
		// echo $chapterStr;
		$sql = "SELECT DISTINCT(comic_id) FROM comic_chapter WHERE id NOT IN ($chapterStr)";
		$comic = Yii::app()->db->createCommand($sql)->queryAll();
		$comicId = array();
		foreach ($comic as $one) {
			$comicId[] = $one['comic_id'];
		}
		$comicStr = implode(',', $comicId);
		$sql = "SELECT * FROM comic WHERE id IN ($comicStr)";
		$comic = Yii::app()->db->createCommand($sql)->queryAll();
		print_r($comic);
	}
	
	public function createDownloadedFile() {
		$sql = "SELECT id FROM comic_chapter";
		$chaps = Yii::app()->db->createCommand($sql)->queryAll();
		
		foreach ($chaps as $chap) {
			$sql = "SELECT id, chapter_id, image FROM comic_image WHERE downloaded_file = '' AND chapter_id = " . $chap['id'];
			$images = Yii::app()->db->createCommand($sql)->queryAll();
			// print_r($images);die();
			if (!empty($images)) {
				$i = 0;
				foreach ($images as $one) {
					$i++;
					$imgUrl = $one['image'];
					$pathInfo = pathinfo($imgUrl);
					$extension = $pathInfo['extension'];
					$downFile = date('YmdHis') . '_' . $i . '.' . $extension;
					Yii::app()->db->createCommand("UPDATE comic_image SET downloaded_file = '$downFile' WHERE id = " . $one['id'])->execute();
				}
			}
		}
	}
	
	public function getFirstChar() {
		$comics = Comic::model()->findAll();
		
		foreach ($comics as $one) {
			$first = substr($one->title_vn, 0, 1);
			if (is_numeric($first)) {
				$first = "0-9";
			}
			$sql = "UPDATE comic SET first_char = '" . $first . "' WHERE id = " . $one->id;
			// die($sql);
			Yii::app()->db->createCommand($sql)->execute();
		}
	}
	
}