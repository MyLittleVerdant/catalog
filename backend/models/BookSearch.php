<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Book;

class BookSearch extends Model
{
    public string $id = '';
    public string $title = '';
    public string $isbn = '';

    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['title', 'isbn'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Book::find();

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
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'isbn', $this->isbn]);

        return $dataProvider;
    }
}


