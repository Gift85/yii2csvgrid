<?php

namespace CsvGrid\models;

use \yii\data\ArrayDataProvider;

class SearchModel extends DefaultModel
{
    public function search()
    {
        $this->loadCsv();
        return $this->getProvider();
    }

    protected function getProvider()
    {
        return new ArrayDataProvider([
            'allModels' => array_filter($this->data, [self::class, 'filter']),
            'sort' => [
                'attributes' => $this->header
            ],
        ]);
    }

    protected function filter($row)
    {
        $filter = \Yii::$app->request->getQueryParam('search', '');
        if (strlen($filter) === 0) {
            return true;
        }

        foreach ($row as $item) {
            if (mb_stripos($item, $filter) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getRow($id)
    {
        $this->loadCsv();
        $this->data = $this->data[$id];
    }

    /* жесткий хак */
    public function __get($name)
    {
        return $this->data[$name];
    }

}