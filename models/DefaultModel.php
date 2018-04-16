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
        $this->filename = \Yii::$app->cache->get('csvFileName');

        if (!$this->filename || !file_exists($this->filename)) {
            $this->filename = \Yii::$app->request->getQueryParam('file', '');
            \Yii::$app->cache->set('csvFileName', $this->filename);
        }

        $this->fileObject = new \SplFileObject($this->filename);
        $this->parseCsv();
        return [
            'dataProvider' => $this->getProvider(),
            'columns' =>  $this->getColumns()
        ];
    }

    protected function parseCsv()
    {
        $models = [];
        $this->header = $this->fileObject->fgetcsv();
        $this->fileObject->next();

        while ($this->fileObject->valid()) {
            $fkey = $this->fileObject->key();
            $models[$fkey]['id'] = $fkey;
            $row = $this->fileObject->fgetcsv();

            foreach ($row as $key => $field) {
                $models[$fkey][$this->header[$key]] = $field;
            }

            $this->fileObject->next();
        }

        $this->data = $models;
    }

    protected function filter($row)
    {
        $filter = \Yii::$app->request->getQueryParam('search', '');
        if (strlen($filter) > 0) {
            $hasMatch = false;
//            var_dump(count($row));
            foreach ($row as $item) {
                if (mb_stripos($item, $filter) !== false) {

//                    var_dump($row['id']);
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
            'allModels' =>  array_filter($this->data, [self::class, 'filter']),
            'sort' => [
                'attributes' => array_merge(['id'], $this->header)
            ],
        ]);
    }

    protected function getColumns()
    {
        $columns = [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{update}{delete}',
//                    'urlCreator' => function($action, $model, $key, $index) {
//                        return ;
//                    },
//                    'buttons' => [
//                        'update' => function ($url, $model, $key) {
//                            return true;
//                        },
//                        'delete' => function ($url, $model, $key) {
//                            return \yii\helpers\Html::a('', [$this->delete, 'param' => $model->id]);
//                        }
//                    ]
                ],
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

    public function delete($id)
    {
        unset($this->data[$id]);
    }

    public function commit()
    {
        $temp = implode(',',$this->header) . PHP_EOL;
        foreach ($this->data as $row) {
            $temp .= implode(',', $row) . PHP_EOL;
        }

        file_put_contents($this->filename, $temp);
    }
}