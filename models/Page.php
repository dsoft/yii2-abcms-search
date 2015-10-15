<?php

namespace abcms\search\models;

use Yii;

/**
 * This is the model class for table "search_page".
 *
 * @property integer $id
 * @property string $title
 * @property string $body
 * @property string $url
 * @property string $lang
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'body', 'url', 'lang'], 'required'],
            [['body'], 'string'],
            [['title', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'body' => 'Body',
            'url' => 'Url',
            'lang' => 'Language',
        ];
    }
}
