<?php

namespace abcms\search\models;

use Yii;

/**
 * This is the model class for table "search_static_page".
 *
 * @property integer $id
 * @property string $title
 * @property string $route
 * @property string $contentSelector
 * @property integer $active
 * @property integer $deleted
 */
class StaticPage extends \abcms\library\base\BackendActiveRecord
{

    public static $enableTime = false;
    public static $enableOrdering = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_static_page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'route'], 'required'],
            [['active'], 'integer'],
            [['title', 'contentSelector'], 'string', 'max' => 100],
            [['route'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => \abcms\search\behaviors\PageBehavior::className(),
                'urlAttribute' => 'frontRoute',
                'contentSelectorAttribute' => 'contentSelector',
                'shouldBeSearched' => 'active',
            ],
        ]);
    }

    /**
     * Return the route of the page as array
     * @return array
     */
    public function getFrontRoute()
    {
        return [$this->route];
    }

    /**
     * @todo To be removed, once the search behavior is not dependent on the translation of the models
     * @return \abcms\search\models\StaticPage
     */
    public function translate()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'route' => 'Route',
            'contentSelector' => 'Content Selector',
            'active' => 'Active',
            'deleted' => 'Deleted',
        ];
    }

}
