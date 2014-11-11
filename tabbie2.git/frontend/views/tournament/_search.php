<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\TournamentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tournament-search">
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>
    <div class="row">
        <div class="col-xs-5"><?= $form->field($model, 'name') ?></div>
        <div class="col-xs-2"><?= $form->field($model, 'start_date') ?></div>
        <div class="col-xs-2"><?= $form->field($model, 'end_date') ?></div>
        <div class="form-group col-xs-3 text-right">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>