<?php

namespace app\views\site;

use app\forms\FinderUserAgentForm;
use app\grids\FinderUserAgentGrid;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = 'Find UserAgent';

/**
 * @var $grid FinderUserAgentGrid
 * @var $model FinderUserAgentForm
 */

?>
<br><br><br>
<div class="">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'userAgent') ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'sourceId')
                ->dropDownList($model->getSourceIdOptions(), [
                    'prompt' => 'Select repository'
                ]) ?>
        </div>
        <div class="col-4">

        </div>
    </div>
    <?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>
<hr>
<?= GridView::widget([
    'dataProvider' => $grid->getProvider(),
    'tableOptions' => [
        'class' => 'table table-striped'
    ],
    'columns' => $grid->columns(),
    'afterRow' => [$grid, 'afterResult'],
    'rowOptions' => ['style' => 'background: darkseagreen;']
]) ?>

