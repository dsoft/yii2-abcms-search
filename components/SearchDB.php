<?php

namespace abcms\search\components;

use abcms\search\models\Page;

class SearchDB extends Search
{

    /**
     * @inheritdoc
     */
    public function savePage($id, $title, $body, $url, $lang)
    {
        $model = Page::findOne($id);
        if(!$model) { // New Page
            $model = new Page();
        }
        $model->id = $id;
        $model->title = $title;
        $model->body = $body;
        $model->url = $url;
        $model->lang = $lang;
        $saved = $model->save(false);
        return $saved;
    }

    /**
     * @inheritdoc
     */
    public function removePage($id)
    {
        $model = Page::findOne($id);
        if($model) {
            return $model->delete();
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function find($keyword, $lang = null, $limit = 0, $offset = 0)
    {
        $query = $this->getQuery($keyword, $lang, $limit, $offset);
        $models = $query->all();
        return $models;
    }

    /**
     * @inheritdoc
     */
    public function count($keyword, $lang = null)
    {
        $query = $this->getQuery($keyword, $lang);
        $count = $query->count();
        return $count;
    }

    /**
     * Return the active query for a certain keyword search
     * @param string $keyword
     * @param string $lang
     * @param int $limit
     * @param int $offset
     * @return ActiveQuery
     */
    protected function getQuery($keyword, $lang = null, $limit = 0, $offset = 0)
    {
        $query = Page::find()->andWhere("MATCH (title,body) AGAINST (:keyword IN BOOLEAN MODE)", [':keyword' => $keyword]);
        if($lang) {
            $query->andWhere(['lang' => $lang]);
        }
        if($limit) {
            $query->limit($limit)->offset($offset);
        }
        return $query;
    }

}
