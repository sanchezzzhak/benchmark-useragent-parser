<?php

namespace app\views\site;

use app\forms\FinderUserAgentForm;
use app\grids\FinderUserAgentGrid;
use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\forms\ActiveAddonField;

$this->title = 'Find UserAgent';

/**
 * @var $grid FinderUserAgentGrid
 * @var $model FinderUserAgentForm
 */

$templateAddonBase = sprintf("{label}\n %s {input}\n{hint}\n{error} ", ' <div class="input-group-prepend">%s</div>');

?>

<h4>Result parse</h4>

<div class="finder-ua">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">

        <div class="col-3">
            <div></div>
            <?= $form->field($model, 'userAgent') ?>

        </div>
        <div class="col-3">
            <div></div>
            <?= $form->field($model, 'sourceId')
                ->dropDownList($model->getSourceIdOptions(), [
                    'prompt' => 'Select source UA repository'
                ]) ?>

        </div>

        <div class="col-3">
            <div></div>
            <?= $form->field($model, 'brandName') ?>

        </div>

        <div class="col-3">
            <div class="legend"><?= Html::activeCheckbox($model, 'emptyModelName') ?></div>
            <?= $form->field($model, 'modelName', [
                'class' => ActiveAddonField::class
            ])->addon(Html::activeCheckbox($model, 'notModelName', ['label' => false])) ?>

        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'parserId')
                ->dropDownList($model->getSourceIdOptions(), [
                    'prompt' => 'Select result UA repository'
                ]) ?>
        </div>
    </div>
    <?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>
<hr>
<?= GridView::widget($grid->getConfig()) ?>

<?php
$this->registerJs(<<<'JS'
    $('.btn[data-action]').on('click', function (e) {
      let json = JSON.parse($(this).attr('data-json'));
      $('#detail-parse').modal('show');
      $('#detail-parse').find('.modal-body')
      .html('<pre>' + JSON.stringify(json, null, 2) + '</pre>');
    });
JS
);
?>


<?= Modal::widget([
    'title' => 'Detail parse result',
    'id' => 'detail-parse'
]); ?>
