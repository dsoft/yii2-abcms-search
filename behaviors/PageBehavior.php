<?php

namespace abcms\search\behaviors;

use Yii;
use abcms\search\models\Entry;
use yii\base\InvalidConfigException;
use Zend\Dom\Query;
use yii\helpers\HtmlPurifier;

class PageBehavior extends ModelBehavior
{

    /**
     * @var string|Closure
     * The css selector of the content container
     */
    public $contentSelector = '';
    
    /**
     * @var string
     * The owner attribute of the css selector of the content container
     */
    public $contentSelectorAttribute = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!$this->url && !$this->urlAttribute) {
            throw new InvalidConfigException('"url" or "urlAttribute" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    protected function returnEntry($lang)
    {
        $url = $this->getSerializedRoute();
        $entry = Entry::find()->where(['url' => $url, 'lang' => $lang, 'typeId' => self::TYPE_INTERNAL_PAGE])->one();
        if(!$entry) {
            $entry = new Entry();
            $entry->url = $url;
            $entry->typeId = self::TYPE_INTERNAL_PAGE;
            $entry->lang = $lang;
            $entry->save(false);
        }
        return $entry;
    }

    /**
     * @inheritdoc
     */
    protected function returnEntries()
    {
        $url = $this->getSerializedRoute();
        $entries = Entry::find()->where(['url' => $url, 'typeId' => self::TYPE_INTERNAL_PAGE])->all();
        return $entries;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        $title = '';
        $html = $this->getHtmlFromRoute();
        $dom = new Query($html);
        $results = $dom->execute('title');
        if(isset($results[0]->textContent)) {
            $title = $results[0]->textContent;
        }
        if(!$title) {
            throw new InvalidConfigException('Unable to find title for the page');
        }
        return $title;
    }

    /**
     * @inheritdoc
     */
    protected function getDescription()
    {
        $html = $this->getHtmlFromRoute();
        $contentSelector = $this->getContentSelector();
        if($contentSelector) {
            $dom = new Query($html, 'utf-8');
            $results = $dom->execute($contentSelector);
            if(isset($results[0])) {
                $html = $results->getDocument()->saveHTML($results[0]);
            }
        }
        $html = HtmlPurifier::process($html);
        $description = preg_replace('/\s+/', ' ', strip_tags($html));
        return $description;
    }

    /**
     * @inheritdoc
     */
    protected function getUrl()
    {
        $route = $this->getRoute();
        $url = \yii\helpers\Url::to($route);
        return $url;
    }

    /**
     * Return the route of the page as an array
     * @return array
     * @throws InvalidConfigException if url configuration is not an array
     */
    protected function getRoute()
    {
        $route = parent::getUrl();
        if(!is_array($route)) {
            throw new InvalidConfigException('"url" or "urlAttribute" property must be an array.');
        }
        return $route;
    }

    /**
     * Return route array as json string
     * @return string
     */
    protected function getSerializedRoute()
    {
        $route = $this->getRoute();
        return serialize($route);
    }

    /**
     * Call internal route and get html string of the page
     * @return string
     */
    protected function getHtmlFromRoute()
    {
        $route = $this->getRoute();
        $route['lang'] = Yii::$app->language;
        $url = \yii\helpers\Url::to($route, true);
        $html = file_get_contents($url);
        return $html;
    }

    /**
     * Return css content selector of the part of the page that should be searched
     * Check first if [[contentSelector]] variable is set, otherwise get the [[contentSelectorAttribute]]
     * @return string
     */
    protected function getContentSelector()
    {
        $owner = $this->owner;
        $result = $this->contentSelector ? $this->processParam('contentSelector') : $this->contentSelectorAttribute ? $owner->{$this->contentSelectorAttribute} : '';
        return $result;
    }

}
