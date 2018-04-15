<?php

namespace Gift85\csvGrid;

/**
 * modules module definition class
 */
class Module extends \yii\base\module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'Gift85\csvGrid\controllers';

    public $controllerModel = 'Gift85\\csvGrid\models\DefaultModel';
}
