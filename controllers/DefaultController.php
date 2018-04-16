<?php

namespace CsvGrid\controllers;

use yii\web\Controller;

/**
 * Default controller for the `modules` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $data = (new $this->module->controllerModel())->prepare();
        return $this->render('index', ['data' => $data]);
    }
}
