<?php

use \yii\helpers\Html;
use \yii\widgets\ActiveForm;

?>
<div class="modules-default-index">
    <div class="csv-form">

        <?php $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]);
        ?>

        <?php foreach ($model->getData() as $name => $cell): ?>
            <?= $form->field($model, $name)->textInput() ?>
        <?php endforeach;?>

        <div class="form-group">
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
