<?php

namespace abcms\search\components;

use abcms\search\models\Page;

class SearchDB extends Search
{

    /**
     * @inheritdoc
     */
    public function savePage($id, $title, $body, $url)
    {
        $model = Page::findOne($id);
        if(!$model){ // New Page
            $model = new Page();
        }
        $model->id;
        $model->title = $title;
        $model->body = $body;
        $model->url = $url;
        $saved = $model->save(FALSE);
        return $saved;
    }

    /**
     * @inheritdoc
     */
    public function removePage($id)
    {
        $model = Page::findOne($id);
        if($model){
            return $model->delete();
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function find($keyword)
    {
        return Page::find()->andWhere("(title,body) AGAINST (':keyword' IN BOOLEAN MODE)", [':keyword'=>$keyword]);
    }

}
