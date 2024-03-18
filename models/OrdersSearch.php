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
//        var_dump($params);
        $query = Orders::find();
        $is_manager = false;
        switch ($session['role_id']){
            case 1:
                $statuses = ['0','1','2','4'];
                $manager_array = [];
                break;
            case 2:
                $statuses = ['1','2','4'];
                $manager_array = ['user_id' => $session['user_id']];
                break;
            case 3:
                $statuses = ['1','2','4'];
                $manager = ManagerDeliverCondition::find()->where(['deliver_id' => $session['user_id']])->andWhere(['status' => '1'])->asArray()->all();
                $is_manager = true;
                $manager_array_list = [];
                for ($k = 0; $k < count($manager); $k++){
                    array_push($manager_array_list,$manager[$k]['manager_id']);
                }
                $manager_array = ['user_id' => $manager_array_list];
                break;
            case 4:
                $statuses = ['1','2','4'];
                $manager_array = [];
        }
        if (empty($params)){
            $query->where(['status' => $statuses])->andWhere($manager_array);
        }
        if (isset($params['type']) && $params['type'] == 'product'){
             $query->select('orders.status,users.name as user_name,nomenclature.name,SUM(order_items.count) as count, AVG(order_items.price_before_discount / order_items.count) as price, orders.orders_date')
                ->leftJoin('order_items','order_items.order_id = orders.id')
                ->leftJoin('nomenclature','nomenclature.id = order_items.nom_id_for_name')
                ->leftJoin('users','users.id = orders.user_id');

        }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
            if (isset($params['numberVal'])) {
                if ($params['numberVal'] == 3 || $params['numberVal'] == 4){
                    $query->where(['status' => $statuses]);
                }elseif ($params['numberVal'] == 1){
                    $query->where(['status' => '1']);
                }elseif ($params['numberVal'] == 2){
                    $query->where(['status' => ['2','4']]);
                }elseif ($params['numberVal'] == 0){
                    $query->where(['status' => '0']);
                }
            }
        }
        if ($is_manager){
            $manager_id = [];
            for ($j = 0; $j < count($manager); $j++){
                array_push($manager_id,$manager[$j]['manager_id']);
            }
            if (isset($params['type']) && $params['type'] == 'product'){
                $query->where(['orders.user_id' => $manager_id]);
            }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
                $query->andWhere(['user_id' => $manager_id]);
            }
        }else{
            if (isset($params['managerId']) && $params['managerId'] != 'null') {
                if (isset($params['type']) && $params['type'] == 'product'){
                    $query->andWhere(['orders.user_id' => $params['managerId']]);
                }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
                    $query->andWhere(['user_id' => $params['managerId']]);
                }
            }
        }

        if (isset($params['clientsVal']) && $params['clientsVal'] != 'null') {
            if (isset($params['type']) && $params['type'] == 'product'){
                $query->andWhere(['orders.clients_id' => $params['clientsVal']]);
            }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
                $query->andWhere(['clients_id' => $params['clientsVal']]);
            }
        }
        if (isset($params['printType']) && $params['printType'] != 'null') {
            if (isset($params['type']) && $params['type'] == 'product'){
                $query->andWhere(['orders.is_exist_company' => $params['printType']]);
            }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
                $query->andWhere(['orders.is_exist_company' => $params['printType']]);
            }
        }
        if (!empty($params['ordersDate'])) {
            if (isset($params['type']) && $params['type'] == 'product'){
                $query->andWhere(['DATE_FORMAT(orders.orders_date, "%Y-%m-%d")' => $params['ordersDate']]);
            }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
                $query->andWhere(['DATE_FORMAT(orders_date, "%Y-%m-%d")' => $params['ordersDate']]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
//        $query->andFilterWhere([
//            'id' => $this->id,
//            'user_id' => $this->user_id,
//            'total_price' => $this->total_price,
//            'total_count' => $this->total_count,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//        ]);
//
//        $query->andFilterWhere(['like', 'status', $this->status]);
        if (isset($params['type']) && $params['type'] == 'product'){
            $dataProvider = $query->andWhere(['orders.status' => '1'])->groupBy(['order_items.nom_id_for_name', 'orders.user_id', 'DATE_FORMAT(orders.orders_date, "%Y-%m-%d")'])
                ->orderBy(['users.name'=> SORT_ASC,'nomenclature.name' => SORT_ASC])->asArray()->all();
            return $dataProvider;
        }elseif (isset($params['type']) && $params['type'] == 'order' || !isset($params['type'])){
            return $dataProvider;
        }

    }
}
