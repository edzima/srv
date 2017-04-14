<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Test;

/**
 * TestSearch represents the model behind the search form of `common\models\Test`.
 */
class TestSearch extends Test
{
    /**
     * @inheritdoc
     */
     public $vehicle;
     public $user;
    public function rules()
    {
        return [
            [['id', 'user_id', 'vehicle_id'], 'integer'],
            [['start_at', 'finish_at','vehicle', 'user'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Test::find();

        $query->joinWith(['vehicle', 'user']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'vehicle_id' => $this->vehicle_id,
            'start_at' => $this->start_at,
            'finish_at' => $this->finish_at,
        ]);

        $query->andFilterWhere(['like', 'vehicle.name', $this->vehicle])
              ->andFilterWhere(['like', 'user.username', $this->user]);

        return $dataProvider;
    }
}
