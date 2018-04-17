<?php

namespace CsvGrid\models;

use \yii\base\Model;
use \yii\data\ArrayDataProvider;

class DefaultModel extends Model
{
    public $filename;
    protected $data;
    protected $header;

    /** @var \SplFileObject */
    protected $fileObject;

    public function loadCsv()
    {
        $this->filename = \Yii::$app->request->getQueryParam('file', null) ?? \Yii::$app->cache->get('csvFileName');
        if (file_exists($this->filename)) {
            \Yii::$app->cache->set('csvFileName', $this->filename);
        } else {
            throw new \Exception('no file no deal');
        }

        $this->fileObject = new \SplFileObject($this->filename);
        $this->parseCsv();
    }

    protected function parseCsv()
    {
        $models = [];
        $this->header = $this->fileObject->fgetcsv();
        $this->fileObject->next();

        while ($this->fileObject->valid()) {
            $fkey = $this->fileObject->key();
            $row = $this->fileObject->fgetcsv();

            foreach ($row as $key => $field) {
                $models[$fkey][$this->header[$key]] = $field;
            }

            $this->fileObject->next();
        }

        $this->data = $models;
    }

    public function getColumns()
    {
        $columns = [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
                'header' => 'Действия',
            ],
            ['class' => 'yii\grid\SerialColumn'],
        ];

        foreach ($this->header as $field) [
            $columns[] = [
                'attribute' => $field,
                'label' => $field,
            ]
        ];

        return $columns;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setDataRow($id, $row)
    {
        $this->loadCsv();
        $this->data[$id] = $row;
    }

    public function delete($id)
    {
        $this->loadCsv();
        unset($this->data[$id]);
    }

    public function commit()
    {
        $temp = implode(',', $this->header) . PHP_EOL;

        foreach ($this->data as $row) {
            $temp .= implode(',', $row) . PHP_EOL;
        }

        file_put_contents($this->filename, $temp);
    }
}