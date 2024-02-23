<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Products;

/**
 * ProductsSearch represents the model behind the search form of `app\models\Products`.
 */
class ProductsSearch extends Products
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'warehouse_id', 'nomenclature_id', 'count'], 'integer'],
            [['price'], 'number'],
            [['status', 'created_at', 'updated_at'], 'safe'],
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
        $session = Yii::$app->session;
        $query = Products::find()->select('id,warehouse_id,nomenclature_id,SUM(count_balance) as count,AVG(price) as price');
        if ($session['role_id'] == '1' || $session['role_id'] == '2'){
            if (isset($params['numberVal']) && $params['numberVal'] != 0){
                $query->andWhere(['status' => '1'])->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])->andWhere(['warehouse_id' => $params['numberVal']]);
            }else{
                $query->Where(['status' => '1'])->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]]);
            }
        } elseif ($session['role_id'] == '4'){
            $users = Users::findOne($session['user_id']);
            $query->andWhere(['status' => '1'])->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])->andWhere(['warehouse_id' => $users->warehouse_id]);
        }else{
            $query->andWhere(['status' => '1'])->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
                'query' => $query->groupBy('warehouse_id, nomenclature_id')->having(['!=', 'SUM(count_balance)', 0]),
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
            'warehouse_id' => $this->warehouse_id,
            'nomenclature_id' => $this->nomenclature_id,
            'count' => $this->count,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
