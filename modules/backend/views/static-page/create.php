<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model abcms\search\models\StaticPage */

$this->title = 'Create Static Page';
$this->params['breadcrumbs'][] = ['label' => 'Static Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-page-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
