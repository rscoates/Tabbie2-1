<?php

	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model common\models\Team */

	$this->title = Yii::t('app', 'Import {modelClass}', [
		'modelClass' => 'Adjudicator',
	]);
	$tournament = $this->context->_getContext();
	$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Adjudicators'), 'url' => ['index', 'tournament_id' => $tournament->id]];
	$this->params['breadcrumbs'][] = $this->title;
?>
	<td class="team-import">

	<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<? if (isset($model->tempImport)): ?>
	<table class="table">
		<tr>
			<th><?= Yii::t("app", "Society") ?></th>
			<th><?= Yii::t("app", "User") ?></th>
			<th><?= Yii::t("app", "Strength") ?></th>
		</tr>
		<? for ($i = 1; $i <= count($model->tempImport); $i++): ?>
			<tr>
				<?
					if (!isset($model->tempImport[$i])) continue;

					$societyField = $model->tempImport[$i][0];
					if (count($societyField) == 1) { //NEW
						$class = "new";
						$value = $societyField[0];
					} else if (count($societyField) == 2) { //Found 1 - easy
						$class = "green";
						$value = Html::a($societyField[1]["name"], ["society/view", "id" => $societyField[1]["id"]]);
					} else { //Ups found multiple
						$class = "yellow";
						$options = [];
						for ($a = 1; $a < count($societyField); $a++) {
							$options[$societyField[$a]["id"]] = $societyField[$a]["name"];
						}
					}
				?>
				<td class="<?= $class ?>">
					<?
						if ($class == "green" OR $class == "new") {
							echo $value;
						} else {
							echo Html::dropDownList("field[$i][0]", $societyField[0], $options);
						}
					?>
				</td>

				<?
					$userField = $model->tempImport[$i][1];
					$class = "";
					if (count($userField) == 1) { //NEW
						$class = "new";
						$value = $userField[0] . " " . $model->tempImport[$i][2][0] . " (" . $model->tempImport[$i][3][0] . ")";
					} else if (count($userField) == 2) { //Found 1 - easy
						$class = "green";
						$value = Html::a($userField[1]["name"], ["user/view", "id" => $userField[1]["id"]]);
					} else { //Ups found multiple
						$class = "yellow";
						for ($a = 1; $a < count($userField); $a++) {
							$options[$userField[$a]["id"]] = $userField[$a]["name"];
						}
					}
				?>
				<td class=" <?= $class ?>">
					<?
						if ($class == "green" OR $class == "new") {
							echo $value;
						} else {
							echo Html::dropDownList("field[$i][1]", $userField[0], $options);
						}
					?>
				</td>

				<td class="">
					<?
						echo $model->tempImport[$i][4][0];
					?>
				</td>
			</tr>
		<? endfor; ?>
	</table>
	<div class="form-group">
		<?= Html::hiddenInput("csvFile", serialize($model->tempImport)); ?>
		<?= Html::hiddenInput("makeItSo", "true"); ?>
		<?= Html::submitButton(Yii::t('app', 'Make it so'), ['class' => 'btn btn-success btn-block loading'])
		?>
	</div>

<? else: ?>
	<div class="team-form">
		<div class="row">
			<div class="col-xs-12">
				<?=
					$form->field($model, 'csvFile')->fileInput([
						'accept' => '.csv'
					])
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($model, 'is_test')->checkbox(); ?>
			</div>
		</div>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
		</div>


	</div>
<? endif; ?>

<?php ActiveForm::end(); ?>