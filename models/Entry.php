<?php

namespace abcms\search\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "search_entry".
 *
 * @property integer $id
 * @property integer $typeId
 * @property integer $modelId
 * @property integer $pk
 * @property string $url
 * @property string $updatedTime
 * @property string $lang
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
            [['typeId', 'lang'], 'required'],
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
            'lang' => 'Language',
        ];
    }
    
    public function updateTime(){
        $this->updatedTime = new Expression('NOW()');
        $this->save(false);
    }
}
