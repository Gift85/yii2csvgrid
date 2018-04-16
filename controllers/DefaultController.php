<?php

namespace CsvGrid\controllers;

use yii\web\Controller;

/**
 * Default controller for the `modules` module
 */
class DefaultController extends Controller
{
    protected $data;
    protected $model;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->getData();
        return $this->render('index', ['data' => $this->data]);
    }

    public function actionUpdate()
    {
        $this->getData();
        var_dump($this->data);
//        $data = (new $this->module->controllerModel())->prepare();
//        return $this->render('index', ['data' => $data]);
    }
    public function actionDelete()
    {
        $id = \Yii::$app->request->getQueryParam('id');
        if ($id === null) {
            return $this->render('empty');
        }
        $this->getData();
//        var_dump($this->data);
        $this->model->delete($id);
        $this->model->commit();
//        $data = (new $this->module->controllerModel())->prepare();
//        return $this->render('index', ['data' => $data]);
    }
    public function getData() {
        $this->model = new $this->module->controllerModel();
        $this->data = $this->model->prepare();
    }
}
