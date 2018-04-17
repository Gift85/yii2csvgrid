<?php

namespace CsvGrid\controllers;

use \yii\web\Controller;
use CsvGrid\models\SearchModel;

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
        $searchModel = new SearchModel();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'columns' =>  $searchModel->getColumns()
        ]);
    }

    public function actionUpdate($id)
    {
        $searchModel = new SearchModel();

        $post = \Yii::$app->request->post();
        if ($post) {
            $searchModel->setDataRow($id, $post['SearchModel']);
            $searchModel->commit();
            return $this->redirect(['index']);
        }

        $searchModel->getRow($id);

        return $this->render('update', [
            'model' => $searchModel,
        ]);
    }

    public function actionDelete($id)
    {
        if ($id === null) {
            return $this->render('empty');
        }

        $searchModel = new SearchModel();
        $searchModel->delete($id);
        $searchModel->commit();
        return $this->redirect(['index']);
    }
}
