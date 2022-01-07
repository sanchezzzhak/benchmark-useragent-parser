<?php

namespace app\views\site;

use app\forms\FinderUserAgentForm;
use app\grids\FinderUserAgentGrid;
use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\Url;
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
            <?= $form->field($model, 'deviceType') ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'clientType') ?>
        </div>
        <div class="col-3">
            <div></div>
            <?= $form->field($model, 'brandName') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'parserId', [
                'class' => ActiveAddonField::class
            ])
            ->dropDownList($model->getSourceIdOptions(), [
                'prompt' => 'Select result UA repository',
                'data-widget' => 'select2',
                'multiple' => 'multiple'
            ])->addon(Html::activeCheckbox($model, 'excludeParserId', [
                'label' => false,
                'title' => 'exclude source'
            ]))?>
        </div>
        <div class="col-3">
            <div></div>
            <?= $form->field($model, 'sourceId', [
                'class' => ActiveAddonField::class
            ])->dropDownList($model->getSourceIdOptions(), [
                'prompt' => 'Select source UA repository',
                'data-widget' => 'select2',
                'multiple' => 'multiple'
            ])->addon(Html::activeCheckbox($model, 'excludeSourceId', [
                'label' => false,
                'title' => 'exclude source'
            ]))?>
        </div>
        <div class="col-3"><?= $form->field($model, 'clientName') ?></div>
        <div class="col-3">
            <div class="legend"><?= Html::activeCheckbox($model, 'emptyModelName') ?></div>
            <?= $form->field($model, 'modelName', [
                'class' => ActiveAddonField::class
            ])->addon(Html::activeCheckbox($model, 'excludeModelName', [
                'label' => false,
                'title' => 'exclude model name'
            ])) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'osName') ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'osVersion') ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'clientVersion') ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'isBot')
                ->dropDownList($model->getBooleanOptions(), [
                    'prompt' => 'Select stage isBot',
                ]) ?>
        </div>
    </div>
    <?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>
<hr>
<?= GridView::widget($grid->getConfig()) ?>

<?php

$url = Url::to(['site/detect']);

$this->registerJs(<<<JS
    $('.btn[data-action="detail"]').on('click', function (e) {
      let json = JSON.parse($(this).attr('data-json'));
      $('#detail-parse').modal('show');
      $('#detail-parse').find('.modal-body')
      .html('<pre>' + JSON.stringify(json, null, 2) + '</pre>');
    });

    $('.btn[data-action="re-detect"]').on('click', function (e) {
      $.ajax('$url', {method: 'get', data: {id: $(this).data('id'), parser: $(this).data('parser')}})
      .done(function (data){
        location.reload();
      });
    });
    
    $('select[data-widget="select2"]').select2()
JS
);
?>

<?= Modal::widget([
    'title' => 'Detail parse result',
    'id' => 'detail-parse'
]); ?>
