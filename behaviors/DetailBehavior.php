<?php

namespace abcms\search\behaviors;

use Yii;
use abcms\search\models\Entry;
use yii\base\InvalidConfigException;

class DetailBehavior extends ModelBehavior
{

    /**
     * @inheritdoc
     */
    public $titleAttribute = 'metaTitle';

    /**
     * @inheritdoc
     */
    public $descriptionAttribute = 'descriptionText';

    /**
     * @inheritdoc
     */
    public $urlAttribute = 'frontUrl';

    /**
     * @inheritdoc
     */
    public $pkAttribute = 'id';

    /**
     * @inheritdoc
     */
    public $shouldBeSearched = 'active';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!$this->title && !$this->titleAttribute) {
            throw new InvalidConfigException('"title" or "titleAttribute" property must be set.');
        }
        if(!$this->url && !$this->urlAttribute) {
            throw new InvalidConfigException('"url" or "urlAttribute" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    protected function returnEntry($lang)
    {
        $id = $this->getPk();
        $modelId = $this->returnModelId();
        $entry = Entry::find()->where(['modelId' => $modelId, 'pk' => $id, 'lang' => $lang])->one();
        if(!$entry) {
            $entry = new Entry();
            $entry->modelId = $modelId;
            $entry->pk = $id;
            $entry->typeId = self::TYPE_DETAIL;
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
        $id = $this->getPk();
        $modelId = $this->returnModelId();
        $entries = Entry::find()->where(['modelId' => $modelId, 'pk' => $id, 'typeId' => self::TYPE_DETAIL])->all();
        return $entries;
    }

}
