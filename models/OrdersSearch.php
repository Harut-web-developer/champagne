<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;

/**
 * OrdersSearch represents the model behind the search form of `app\models\Orders`.
 */
class OrdersSearch extends Orders
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'clients_id', 'total_count'], 'integer'],
            [['status', 'created_at', 'updated_at'], 'safe'],
            [['total_price'], 'number'],
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
//        var_dump($params);
        $session = Yii::$app->session;
        $query = Orders::find();
        if ($session['role_id'] == 1){
            if (isset($params['managerId']) && $params['managerId'] == 'null'){
                if(isset($params['numberVal']) && $params['numberVal'] != 3) {
                    $query->andWhere(['status' => $params['numberVal']])->orderBy(['created_at'=> SORT_DESC]);
                }elseif (isset($params['numberVal']) && $params['numberVal'] == 3){
                    $query->andWhere(['status' => '1'])->orderBy(['created_at'=> SORT_DESC]);
                }
            }elseif (isset($params['managerId']) && $params['managerId'] != 'null'){
                if(isset($params['numberVal']) && $params['numberVal'] != 3) {
                    $query->andWhere(['status' => $params['numberVal']])->andWhere(['user_id' => $params['managerId']])->orderBy(['created_at'=> SORT_DESC]);
                }elseif (isset($params['numberVal']) && $params['numberVal'] == 3){
                    $query->andWhere(['status' => '1'])->andWhere(['user_id' => $params['managerId']])->orderBy(['created_at'=> SORT_DESC]);
                }
            }

        }else {
            if (isset($params['numberVal']) && $params['numberVal'] != 4) {
                $query->andWhere(['status' => $params['numberVal']])->andWhere(['user_id' => $params['managerId']])->orderBy(['created_at'=> SORT_DESC]);;
            }elseif (isset($params['numberVal']) && $params['numberVal'] == 4){
                $query->andWhere(['status' => '1'])->andWhere(['user_id' => $params['managerId']])->orderBy(['created_at'=> SORT_DESC]);;
            }
        }
        if ($session['role_id'] == 1){
            $res =  $query->orderBy(['created_at'=> SORT_DESC]);
        }else{
            $res =  $query->andWhere(['user_id' => $session['user_id']])->orderBy(['created_at'=> SORT_DESC]);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $res,
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
            'total_price' => $this->total_price,
            'total_count' => $this->total_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
