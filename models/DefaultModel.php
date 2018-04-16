<?php

namespace CsvGrid\models;

use yii\data\ArrayDataProvider;

class DefaultModel
{
    public $filename;
    protected $data;
    protected $header;

    /** @var \SplFileObject */
    protected $fileObject;

    public function prepare()
    {
        $this->filename = \Yii::$app->request->getQueryParam('file', '');
        $this->fileObject = new \SplFileObject($this->filename);
        $this->prepareData();
        return [
            'dataProvider' => $this->getProvider(),
//            'filterModel' => $searchModel,
            'columns' =>  $this->getColumns()
        ];
    }

    protected function prepareData()
    {
        $models = [];
        $this->header = $this->fileObject->fgetcsv();
        $this->fileObject->next();
        $i = 0;

        while ($this->fileObject->valid()) {
            $models[$i]['id'] = $i;
            $row = $this->fileObject->fgetcsv();

            foreach ($row as $key => $field) {
                $models[$i][$this->header[$key]] = $field;
            }

            $this->fileObject->next();
            $i++;
        }

        $this->data = array_filter($models, [self::class, 'filter']);
    }

    protected function filter($row)
    {
        $filter = \Yii::$app->request->getQueryParam('search', '');
        if (strlen($filter) > 0) {
            $hasMatch = false;
            foreach ($row as $item) {
                if (strpos($item, $filter) != false) {
                    $hasMatch = true;
                    break;
                }
            }
            return $hasMatch;
        } else {
            return true;
        }
    }

    protected function getProvider()
    {
        return new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $this->data,
            'sort' => [
                'attributes' => array_merge(['id'], $this->header)
            ],
        ]);
    }

    protected function getColumns()
    {
        $columns = [
                ['class' => 'yii\grid\ActionColumn'],
                ['class' => 'yii\grid\SerialColumn'],
                'id',
            ];

        foreach($this->header as $field) [
            $columns[] = [
                'attribute' => $field,
                'value' => $field,
            ]
        ];
        return $columns;
    }
}