<?php

namespace app\models;

use app\models\ManagerDeliverCondition;
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
        $session = Yii::$app->session;
//echo "<pre>";
        $query = Orders::find();
//        $product =  $query->select('users.name as user_name,nomenclature.name,SUM(order_items.count) as count, AVG(price_before_discount) as price, orders.orders_date')->leftJoin('order_items','order_items.order_id = orders.id')
//            ->leftJoin('nomenclature','nomenclature.id = order_items.nom_id_for_name')
//            ->leftJoin('users','users.id = orders.user_id')
//            ->groupBy(['order_items.nom_id_for_name', 'orders.user_id'])
//            ->asArray()
//            ->all();
//        var_dump($product);
//        exit();
        $is_manager = false;
        switch ($session['role_id']){
            case 1:
                $statuses = [0,1,2];
                break;
            case 2:
                $statuses = [1,2];
                break;
            case 3:
                $statuses = [1,2];
//                $is_manager = Yii::$
        }
        if (isset($params['numberVal']) && !empty($params['numberVal'])) {
                $query->where(['status' => intval($params['numberVal'])]);
        }  else {
                $query->where(['status' => $statuses]);
        }
//        if()
        if (isset($params['managerId']) && $params['managerId'] != 'null') {
            $query->andWhere(['user_id' => $params['managerId']]);
        }
        if (isset($params['clientsVal']) && $params['clientsVal'] != 'null') {
            $query->andWhere(['clients_id' => $params['clientsVal']]);
        }
        if (!empty($params['ordersDate'])) {
            $query->andWhere(['DATE_FORMAT(orders_date, "%Y-%m-%d")' => $params['ordersDate']]);
        }

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
            'total_price' => $this->total_price,
            'total_count' => $this->total_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
