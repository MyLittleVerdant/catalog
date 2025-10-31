<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Author;

class AuthorSearch extends Model
{
    public string $id ='';
    public string $full_name='';

    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['full_name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Author::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'full_name', $this->full_name]);

        return $dataProvider;
    }
}

