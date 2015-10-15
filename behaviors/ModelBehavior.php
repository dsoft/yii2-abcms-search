<?php

namespace abcms\search\behaviors;

use Yii;
use abcms\library\models\Model;
use yii\db\BaseActiveRecord;
use Closure;

abstract class ModelBehavior extends \yii\base\Behavior
{

    /**
     * Search entry type: detail, where each model has a search entry,
     * Identified by modelId and primary key
     */
    const TYPE_DETAIL = 1;
    
    /**
     * Search entry type: internal page, where all models have one search entry,
     * Identified by page url
     */
    const TYPE_INTERNAL_PAGE = 2;

    /**
     * @var int|Closure
     * Primary key of the model that should be added to the search
     * Could be an anonymous function with the owner object passed as parameter, or integer
     */
    public $pk = null;

    /**
     * @var string|Closure
     * Title of the page that should be added to the search
     * Could be an anonymous function with the owner object passed as parameter, or string
     */
    public $title = '';

    /**
     * @var string|Closure
     * Body text of the page
     * Could be an anonymous function with the owner object passed as parameter, or string
     */
    public $description = '';

    /**
     * @var string|Closure
     * URL of the page
     * Could be an anonymous function with the owner object passed as parameter, or string
     */
    public $url = '';

    /**
     * @var boolean|string|Closure
     * If page should be included in the search db or not
     * variable can be: anonymous function, variable in the owner object or boolean
     */
    public $shouldBeSearched = true;

    /**
     * @var string the title attribute in the owner object
     */
    public $titleAttribute = '';

    /**
     * @var string the description attribute in the owner object
     */
    public $descriptionAttribute = '';

    /**
     * @var string the url attribute in the owner object
     */
    public $urlAttribute = '';

    /**
     * @var string the primary key attribute in the owner object
     */
    public $pkAttribute = '';

    /**
     * id of the owner model in the model table
     * @var string
     */
    private $_modelId = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'processSearch',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'processSearch',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'removeFromSearch',
        ];
    }

    /**
     * Add the search entry in the database if it doesn't exist and return it.
     * @param string $lang
     * @return \abcms\search\models\Entry
     */
    abstract protected function returnEntry($lang);

    /**
     * Return searh entries related to this model
     * @return array
     */
    abstract protected function returnEntries();

    /**
     * If model should be searched, add it to the database and add all translation
     * otherwise remove it from search
     */
    public function processSearch()
    {
        if($this->getShouldBeSearched()) {
            $this->addToSearch();
            $this->addOtherLanguagesToSearch();
        }
        else {
            $this->removeFromSearch();
        }
    }

    /**
     * Add page to search engine
     */
    public function addToSearch($lang = null)
    {
        if(!$lang) {
            $lang = $this->getSourceLanguage();
        }
        $entry = $this->returnEntry($lang);
        $pageId = $entry->id;
        $title = $this->getTitle();
        $description = $this->getDescription();

        $url = $this->getUrl();
        if(Yii::$app->search->savePage($pageId, $title, $description, $url, $lang)) {
            $entry->updateTime();
        }
    }

    /**
     * Remove page from search
     */
    public function removeFromSearch()
    {
        $entries = $this->returnEntries();
        foreach($entries as $entry) {
            $entryId = $entry->id;
            $entry->delete();
            Yii::$app->search->removePage($entryId);
        }
    }

    /**
     * Translate model to remaining languages and add each one to the serch database
     */
    protected function addOtherLanguagesToSearch()
    {
        $source = $this->getSourceLanguage();
        $langs = $this->getLanguages();
        $owner = $this->owner;
        foreach($langs as $lang => $name) {
            if($lang != $source) {
                $originalLang = \Yii::$app->language;
                \Yii::$app->language = $lang;
                $model = $owner->translate($lang);
                $model->addToSearch($lang);
                \Yii::$app->language = $originalLang;
            }
        }
    }

    /**
     * Return [[_modelId]] and get it from the model table if it's not set.
     * @return string
     */
    protected function returnModelId()
    {
        if(!$this->_modelId) {
            /** @var ActiveRecord $owner */
            $owner = $this->owner;
            $this->_modelId = Model::returnModelId($owner->className());
        }
        return $this->_modelId;
    }

    /**
     * Process given parameter
     * parameter can be an anyonymous function or a string
     * @param string $paramName
     * @return mixed
     */
    protected function processParam($paramName)
    {
        $val = $this->$paramName;
        if($val instanceof Closure) {
            return call_user_func($val, $this->owner);
        }
        else {
            return $val;
        }
    }

    /**
     * Return if model should be searched in the frontend or not
     * @return boolean
     */
    protected function getShouldBeSearched()
    {
        $val = $this->shouldBeSearched;
        if(is_string($val)) {
            return $this->owner->$val;
        }
        elseif($val instanceof Closure) {
            return call_user_func($val, $this->owner);
        }
        else {
            return $val;
        }
        return $result;
    }

    /**
     * Return title of the page that should be searched
     * Check first if [[title]] variable is set, otherwise get the [[titleAttribute]]
     * @return string
     */
    protected function getTitle()
    {
        $owner = $this->owner;
        $result = $this->title ? $this->processParam('title') : $owner->{$this->titleAttribute};
        return $result;
    }

    /**
     * Return description of the page that should be searched
     * Check first if [[description]] variable is set, otherwise get the [[descriptionAttribute]]
     * @return string
     */
    protected function getDescription()
    {
        $owner = $this->owner;
        $result = $this->description ? $this->processParam('description') : ($this->descriptionAttribute ? $owner->{$this->descriptionAttribute} : '');
        return $result;
    }

    /**
     * Return url of the page that should be searched
     * Check first if [[url]] variable is set, otherwise get the [[urlAttribute]]
     * @return string
     */
    protected function getUrl()
    {
        $owner = $this->owner;
        $result = $this->url ? $this->processParam('url') : $owner->{$this->urlAttribute};
        return $result;
    }

    /**
     * Return the primary key of the page that should be searched
     * Check first if [[pk]] variable is set, otherwise get the [[pkAttribute]]
     * @return integer
     */
    protected function getPk()
    {
        $owner = $this->owner;
        $result = $this->pk ? $this->processParam('pk') : $owner->{$this->pkAttribute};
        return $result;
    }

    /**
     * return the langages array of the website
     * @return array
     */
    protected function getLanguages()
    {
        if(isset(Yii::$app->params['languages'])) {
            return Yii::$app->params['languages'];
        }
        return [];
    }

    /**
     * Return application source Language
     * @return string
     */
    protected function getSourceLanguage()
    {
        return Yii::$app->sourceLanguage;
    }

}
