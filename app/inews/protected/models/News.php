<?php
/**
 * This is the model class for table "news".
 *
 * The followings are the available columns in table 'news':
 * @property string $id
 * @property string $title
 * @property string $title_en
 * @property string $headline
 * @property string $headline_en
 * @property string $content
 * @property string $content_en
 * @property string $thumbnail_url
 * @property string $category_id
 * @property string $site_id
 * @property string $published_time
 * @property string $created_time
 * @property string $original_url
 * @property integer $youtube_video
 */
class News extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return News1 the static model class
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
        return 'news';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, title_en, headline, headline_en, content, content_en, thumbnail_url, category_id, site_id, published_time, created_time, original_url', 'required'),
            array('youtube_video', 'numerical', 'integerOnly'=>true),
            array('title, title_en, thumbnail_url, original_url', 'length', 'max'=>255),
            array('headline, headline_en, youtube_data', 'length', 'max'=>500),
            array('category_id, site_id', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, title, title_en, headline, headline_en, youtube_data, content, content_en, thumbnail_url, category_id, site_id, published_time, created_time, original_url, youtube_video', 'safe', 'on'=>'search'),
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
            'title_en' => 'Title En',
            'headline' => 'Headline',
            'headline_en' => 'Headline En',
            'content' => 'Content',
            'content_en' => 'Content En',
            'thumbnail_url' => 'Thumbnail Url',
            'category_id' => 'Category',
            'site_id' => 'Site',
            'published_time' => 'Published Time',
            'created_time' => 'Created Time',
            'original_url' => 'Original Url',
            'youtube_video' => 'Youtube Video',
            'youtube_data' => 'Youtube Data',
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
        $criteria->compare('title_en',$this->title_en,true);
        $criteria->compare('headline',$this->headline,true);
        $criteria->compare('headline_en',$this->headline_en,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('content_en',$this->content_en,true);
        $criteria->compare('thumbnail_url',$this->thumbnail_url,true);
        $criteria->compare('category_id',$this->category_id,true);
        $criteria->compare('site_id',$this->site_id,true);
        $criteria->compare('published_time',$this->published_time,true);
        $criteria->compare('created_time',$this->created_time,true);
        $criteria->compare('original_url',$this->original_url,true);
        $criteria->compare('youtube_video',$this->youtube_video);
        $criteria->compare('youtube_data',$this->youtube_data);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
	
	public static function isExist($siteId, $articleUrl) {
		$criteria=new CDbCriteria;
		$criteria->select = 'id';
		$criteria->condition = "site_id = $siteId AND original_url = '$articleUrl'";
		
		$news = News::model()->findAll($criteria);
		// var_dump($news);die();
		
		return !empty($news) ? true : false;
	}
	
	public function getLatest($siteId, $page = 1, $limit = 20) {
		$params = Yii::app()->params;
		
		$offset = ($page - 1) * $limit;
		$query = Yii::app()->db->createCommand()
			->select('*')
			->from('news')
			->where('site_id = ' . $siteId)
			->order('published_time DESC')
			->limit($limit)
			->offset($offset);
		$news = $query->queryAll();
		// var_dump($news);
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
			->from('news')
			->where('site_id = ' . $siteId)
            ->queryScalar();
		
		return array('data' => $news, 'total' => $count);
	}
	
	public function getFeatured($siteId, $catId, $page = 1, $limit = 5) {
		$params = Yii::app()->params;
		
		$offset = ($page - 1) * $limit;
		$query = Yii::app()->db->createCommand()
			->select('n.*')
			->from('news_featured nf')
			->leftJoin('news n', 'n.id = nf.news_id')
			->where('n.site_id = ' . $siteId . ' AND n.category_id = ' . $catId)
			->order('nf.created_time DESC')
			->limit($limit)
			->offset($offset);
		$news = $query->queryAll();
		// var_dump($news);
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(n.id)')
			->from('news_featured nf')
			->leftJoin('news n', 'n.id = nf.news_id')
			->where('n.site_id = ' . $siteId . ' AND n.category_id = ' . $catId)
            ->queryScalar();
		
		return array('data' => $news, 'total' => $count);
	}
	
	public function getNewsCat($categoryId, $siteId, $excludeId = array(), $page = 1, $limit = 20) {
		$params = Yii::app()->params;
		if (isset($params['not_release'])) $excludeId = array();
		$where = '';
        if (!empty($excludeId)) $where = " AND n.id NOT IN (" . implode(',', $excludeId) . ")";
		
		$offset = ($page - 1) * $limit;
		$query = Yii::app()->db->createCommand()
			->select('n.*')
			->from('news n')
			->where('n.category_id = ' . $categoryId . ' AND n.site_id='.$siteId . $where)
			->order('n.published_time DESC')
			->limit($limit)
			->offset($offset);
		$news = $query->queryAll();
		// var_dump($news);
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(n.id)')
			->from('news n')
			->where('n.category_id = ' . $categoryId . ' AND n.site_id='.$siteId . $where)
            ->queryScalar();
		
		return array('data' => $news, 'total' => $count);
	}
    
	public function getNext($siteId, $newsId, $page = 1, $limit = 20) {
		$params = Yii::app()->params;
		
		$offset = ($page - 1) * $limit;
		$query = Yii::app()->db->createCommand()
			->select('n.*')
			->from('news_featured nf')
			->leftJoin('news n', 'n.id = nf.news_id')
			->where('n.site_id = ' . $siteId)
			->order('nf.published_time DESC')
			->limit($limit)
			->offset($offset);
		$news = $query->queryAll();
		// var_dump($news);
		
		return $news;
	}
    
    public function searchText($keyword, $siteId, $page = 1, $limit = 20) {
		$offset = ($page - 1) * $limit;
        $query = Yii::app()->db->createCommand()
            ->select("id, title, headline, content, thumbnail_url, category_id, published_time, created_time, MATCH(title_en) AGAINST ('$keyword') AS score")
            ->from('news')
            ->where("MATCH(title_en) AGAINST('$keyword') AND site_id = $siteId")
            ->order("score DESC")
            ->limit($limit)
            ->offset($offset);
        $news = $query->queryAll();
        
        $count = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
            ->from('news')
            ->where("MATCH(title_en) AGAINST('$keyword') AND site_id = $siteId")
            ->queryScalar();
        
        return array('data' => $news, 'total' => $count);
    }
	
	public function fixNews() {
		/*
		$query = Yii::app()->db->createCommand("SELECT * FROM news WHERE content like '%width=29000%'");
		$result = $query->queryAll();
		
		foreach ($result as $one) {
			$content = str_replace('width=29000%', 'width=100%', $one['content']);
			$content = str_replace("'", "''", $content);
			echo "Updating ". $one['id'] . "<br/>";
			$sql = Yii::app()->db->createCommand("UPDATE news SET content = '$content' WHERE id = " . $one['id'])->execute();
		}
		*/
		
		$query = Yii::app()->db->createCommand("SELECT * FROM news WHERE content like '%<IMG%'");
		$result = $query->queryAll();
		echo "Total " . count($result);
		foreach ($result as $one) {
			$content = str_replace('<IMG', '<IMG width=290', $one['content']);
			$content = str_replace('<img', '<img width=290', $content);
			$content = str_replace("'", "''", $content);
			echo "Updating ". $one['id'] . "<br/>";
			$sql = Yii::app()->db->createCommand("UPDATE news SET content = '$content' WHERE id = " . $one['id'])->execute();
		}
	}
    
    public function getApp() {
        $data = Yii::app()->db->createCommand("SELECT * FROM app ORDER BY created_time DESC")->queryAll();
        
        return $data;
    }
	
	public function getByUrl($siteId, $url) {
		$url = trim($url);
		/*
		$news = Yii::app()->db->createCommand()
			->select('*')
			->from('news')
			->where("site_id = $siteId AND original_url = '$url'")
			->queryRow();*/
		$news = News::model()->findByAttributes(array('site_id' => $siteId, 'original_url' => $url));
		
		return $news;
	}
	
	public function getSite($siteId) {
		$sql = "SELECT * FROM site WHERE id = $siteId";
		$site = Yii::app()->db->createCommand($sql)->queryRow();
		
		return $site;
	}
	
	public function isXKCNExist($url) {
		$result = Yii::app()->db->createCommand("SELECT id FROM xkcn WHERE thumbnail_url = '$url'")->queryRow();
		
		return !empty($result) ? true : false;
	}
	
}