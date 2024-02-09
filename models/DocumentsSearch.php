<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Documents;

/**
 * DocumentsSearch represents the model behind the search form of `app\models\Documents`.
 */
class DocumentsSearch extends Documents
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'warehouse_id', 'rate_id'], 'integer'],
            [['comment', 'date', 'status', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Documents::find();
        if (isset($params['numberVal']) && $params['numberVal'] != 0){
            if ($params['numberVal'] == 1){
                $query->andWhere(['status' => '1'])->andWhere(['document_type' => $params['numberVal']]);
            }elseif ($params['numberVal'] == 2){
                $query->andWhere(['status' => '1'])->andWhere(['document_type' => $params['numberVal']]);
            }elseif ($params['numberVal'] == 3){
                $query->andWhere(['status' => '1'])->andWhere(['document_type' => $params['numberVal']]);
            }elseif ($params['numberVal'] == 4){
                $query->andWhere(['status' => '1'])->andWhere(['document_type' => $params['numberVal']]);
            }elseif ($params['numberVal'] == 6){
                $query->andWhere(['status' => '1'])->andWhere(['document_type' => $params['numberVal']]);
            }
        }else{
            $query->andWhere(['status' => '1']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at'=> SORT_DESC]),
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
            'warehouse_id' => $this->warehouse_id,
            'rate_id' => $this->rate_id,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
