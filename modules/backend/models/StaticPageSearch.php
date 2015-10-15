<?php

namespace abcms\search\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use abcms\search\models\StaticPage;

/**
 * StaticPageSearch represents the model behind the search form about `abcms\search\models\StaticPage`.
 */
class StaticPageSearch extends StaticPage
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active', 'deleted'], 'integer'],
            [['title', 'route', 'contentSelector'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = StaticPage::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'route', $this->route])
                ->andFilterWhere(['like', 'contentSelector', $this->contentSelector]);

        return $dataProvider;
    }

}
