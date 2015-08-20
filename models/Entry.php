<?php

namespace abcms\search\models;

use Yii;

/**
 * This is the model class for table "search_entry".
 *
 * @property integer $id
 * @property integer $typeId
 * @property integer $modelId
 * @property integer $pk
 * @property string $url
 * @property string $updatedTime
 */
class Entry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_entry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['typeId', 'url'], 'required'],
            [['typeId', 'modelId', 'pk'], 'integer'],
            [['updatedTime'], 'safe'],
            [['url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'typeId' => 'Type',
            'modelId' => 'Model ID',
            'pk' => 'Primary Key',
            'url' => 'Url',
            'updatedTime' => 'Updated Time',
        ];
    }
}
