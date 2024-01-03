<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Nomenclature;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Payments;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class DashboardController extends Controller
{
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        if ($action->id !== 'login' && !(isset($session['user_id']) && $session['logged'])) {
            return $this->redirect(['site/login']);
        } else if ($action->id == 'login' && !(isset($session['user_id']) && $session['logged'])) {
            return $this->actionLogin();
        }
        if(!$session['username']){
            $this->redirect('/site/logout');
        }
        return parent::beforeAction($action);
    }
    public function actionIndex(){
        $payment = 0;
        $sale = 0;
        $deal = 0;
        $chart_round_total = 0;
        if (empty($_GET) || isset($_GET['date']) && !isset($_GET['start_date']) && !isset($_GET['end_date'])){
            if (empty($_GET) || $_GET['date'] === 'day'){
                $order_payment = Payments::find()->select('SUM(payment_sum) as payment')
                    ->where(['status' => '1'])
                    ->andWhere(['=', 'DATE(pay_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $orders_sale = Orders::find()
                    ->select('SUM(total_price) as sale')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $orders_deal = Orders::find()
                    ->select('SUM(total_price) as deal')
                    ->where(['or',['status' => '1'],['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $orders_cost = OrderItems::find()
                    ->select('(SUM(order_items.price) - SUM(order_items.cost)) as profit')
                    ->leftJoin('orders','orders.id = order_items.order_id')
                    ->where(['orders.status' => '3'])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->groupBy('order_items.order_id')
                    ->asArray()
                    ->all();
                $cost = array_sum(array_column($orders_cost,'profit'));
                $total_order = Orders::find()
                    ->select('SUM(total_price) as total')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                if ($total_order[0]['total'] != null){
                    $chart_round_total = $total_order[0]['total'];
                }
                $chart_round_products = OrderItems::find()->select('SUM(order_items.price) as price,nomenclature.name')
                    ->leftJoin('orders','orders.id = order_items.order_id')
                    ->leftJoin('nomenclature', 'order_items.nom_id_for_name = nomenclature.id')
                    ->where(['or',['orders.status' => '2'],['orders.status' => '3']])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->groupBy('order_items.nom_id_for_name')
                    ->asArray()
                    ->all();
                $round_chart_label = [];
                $round_chart_percent = [];
                if (!empty($chart_round_products)){
                    for ($i = 0; $i < count($chart_round_products); $i++){
                        array_push($round_chart_percent,round(($chart_round_products[$i]['price']/$chart_round_total)*100));
                        array_push($round_chart_label,$chart_round_products[$i]['name']);
                    }
                }else{
                    array_push($round_chart_percent,0);
//                    array_push($round_chart_label,'Ապրանք չկա');
                }
                $clients_payment = Orders::find()->select('clients.name, total_price')
                    ->leftJoin('clients', 'orders.clients_id = clients.id')
                    ->where(['orders.status' => '3'])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();

                $line_chart_debt = Orders::find()->select('SUM(total_price) as debt')
                    ->where(['orders.status' => '2'])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $line_chart_label = [];
                $line_chart_number = [];
                if (!empty($line_chart_debt)){
                    for ($i = 0; $i < count($line_chart_debt); $i++){
                        array_push($line_chart_number,$line_chart_debt[0]['debt']);
                        array_push($line_chart_label,'Այսօր');
                    }
                }else{
                    array_push($line_chart_number,0);
                    array_push($line_chart_label,'Այսօր');
                }
                $line_chart_orders = Orders::find()->select('SUM(total_price) as orders')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $line_chart_orders_label = [];
                $line_chart_orders_this_year = [];
                if (!empty($line_chart_orders)){
                    for ($i = 0; $i < count($line_chart_orders); $i++){
                        array_push($line_chart_orders_this_year,$line_chart_orders[0]['orders']);
                        array_push($line_chart_orders_label,'Այսօր');
                    }
                }else{
                    array_push($line_chart_orders_this_year,0);
                    array_push($line_chart_orders_label,'Այսօր');
                }

                $line_chart_orders_last =  Orders::find()->select('SUM(total_price) as orders')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'DATE(orders_date)', date('Y-m-d',strtotime("-1 year"))])
                    ->asArray()
                    ->all();
                $line_chart_orders_last_year = [];
                if (!empty($line_chart_orders_last[0]['orders'])){
                    for ($i = 0; $i < count($line_chart_orders_last); $i++){
                        array_push($line_chart_orders_last_year,$line_chart_orders_last[0]['orders']);
                    }
                }else{
                    array_push($line_chart_orders_last_year,0);
                }

                if ($order_payment[0]['payment'] != null){
                    $payment = $order_payment[0]['payment'];
                }
                if ($orders_sale[0]['sale'] != null){
                    $sale = $orders_sale[0]['sale'];
                }
                if ($orders_deal[0]['deal'] != null){
                    $deal = $orders_deal[0]['deal'];
                }
                if ($total_order[0]['total'] != null){
                    $chart_round_total = $total_order[0]['total'];
                }
            }elseif ($_GET['date'] === 'monthly'){
//        echo "<pre>";
                $order_payment = Payments::find()->select('SUM(payment_sum) as payment')
                    ->where(['status' => '1'])
                    ->andWhere(['=', 'MONTH(pay_date)', date('m')])
                    ->andWhere(['=', 'YEAR(pay_date)', date('Y')])
                    ->asArray()
                    ->all();
                $orders_sale = Orders::find()
                    ->select('SUM(total_price) as sale')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->asArray()
                    ->all();
                $orders_deal = Orders::find()
                    ->select('SUM(total_price) as deal')
                    ->where(['or',['status' => '1'],['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->asArray()
                    ->all();
                $orders_cost = OrderItems::find()
                    ->select('(SUM(order_items.price) - SUM(order_items.cost)) as profit')
                    ->leftJoin('orders','orders.id = order_items.order_id')
                    ->where(['orders.status' => '3'])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('order_items.order_id')
                    ->asArray()
                    ->all();
                $cost = array_sum(array_column($orders_cost,'profit'));
                $total_order = Orders::find()
                    ->select('SUM(total_price) as total')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->asArray()
                    ->all();
                if ($total_order[0]['total'] != null){
                    $chart_round_total = $total_order[0]['total'];
                }
                $chart_round_products = OrderItems::find()->select('SUM(order_items.price) as price,nomenclature.name')
                    ->leftJoin('orders','orders.id = order_items.order_id')
                    ->leftJoin('nomenclature', 'order_items.nom_id_for_name = nomenclature.id')
                    ->where(['or',['orders.status' => '2'],['orders.status' => '3']])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('order_items.nom_id_for_name')
                    ->asArray()
                    ->all();
                $round_chart_label = [];
                $round_chart_percent = [];
                if (!empty($chart_round_products)){
                    for ($i = 0; $i < count($chart_round_products); $i++){
                        array_push($round_chart_percent,round(($chart_round_products[$i]['price']/$chart_round_total)*100));
                        array_push($round_chart_label,$chart_round_products[$i]['name']);
                    }
                }else{
                    array_push($round_chart_percent,0);
//                    array_push($round_chart_label,'Ապրանք չկա');
                }
                $clients_payment = Orders::find()->select('clients.name, total_price')
                    ->leftJoin('clients', 'orders.clients_id = clients.id')
                    ->where(['orders.status' => '3'])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->asArray()
                    ->all();
                $line_chart_debt = Orders::find()->select('SUM(total_price) as debt, DATE(orders_date) as orders_date')
                    ->where(['orders.status' => '2'])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('DATE(orders_date)')
                    ->asArray()
                    ->all();

                $line_chart_label = [];
                $line_chart_number = [];
                if (!empty($line_chart_debt)){
                    for ($k = 0; $k < count($line_chart_debt);$k++){
                        array_push($line_chart_number,$line_chart_debt[$k]['debt']);
                        array_push($line_chart_label,$line_chart_debt[$k]['orders_date']);
                    }
                }else{
                    array_push($line_chart_number,0);
                    array_push($line_chart_label,date('Y-m'));
                }
                $line_chart_orders = Orders::find()->select('SUM(total_price) as orders,DATE(orders_date) as orders_date')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'MONTH(orders_date)', date('m')])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('DATE(orders_date)')
                    ->asArray()
                    ->all();
//                var_dump($line_chart_orders);
                $line_chart_orders_label = [];
                $line_chart_orders_this_year = [];
                if (!empty($line_chart_orders)){
                    $days = [];
                    for ($i = 1; $i <= date('d',strtotime('last day of this month'));$i++){
                        if ($i < 10) {
                            $dayData = date('Y-m-' . '0' . $i);
                            array_push($days, $dayData);
                        } else {
                            $dayData = date('Y-m-' . $i);
                            array_push($days, $dayData);
                        }
                    }
                    foreach ($days as $day) {
                        $found = false;
                        foreach ($line_chart_orders as $row) {
                            if ($row["orders_date"] == $day) {
                                $line_chart_orders_label[] = $day;
                                $line_chart_orders_this_year[] = $row["orders"];
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $line_chart_orders_label[] = $day;
                            $line_chart_orders_this_year[] = 0;
                        }
                    }
                }else{
                    array_push($line_chart_orders_this_year,0);
                    array_push($line_chart_orders_label,date('Y-m'));
                }
                $firstDayLastYear = date('Y-m-01', strtotime("-1 year"));
                $lastDayLastYear = date('Y-m-t', strtotime("-1 year"));
                $line_chart_orders_last =  Orders::find()->select('SUM(total_price) as orders,DATE(orders_date) as orders_date')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['>=', 'DATE(orders_date)',$firstDayLastYear])
                    ->andWhere(['<=', 'DATE(orders_date)',$lastDayLastYear])
                    ->groupBy('DATE(orders_date)')
                    ->asArray()
                    ->all();
//                var_dump($line_chart_orders_last);
                $line_chart_orders_last_year = [];
                if (!empty($line_chart_orders_last[0]['orders'])){
                    $lastYearDays = [];
                    for ($i = 1; $i <= date('d',strtotime('last day of this month'));$i++){
                        if ($i < 10) {
                            $dayData = date('Y-m-' . '0' . $i,strtotime("-1 year"));
                            array_push($lastYearDays, $dayData);
                        } else {
                            $dayData = date('Y-m-' . $i,strtotime("-1 year"));
                            array_push($lastYearDays, $dayData);
                        }
                    }
                    foreach ($lastYearDays as $day) {
                        $found = false;
                        foreach ($line_chart_orders_last as $row) {
                            if ($row["orders_date"] == $day) {
                                $line_chart_orders_last_year[] = $row["orders"];
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $line_chart_orders_last_year[] = 0;
                        }
                    }
                }else{
                    array_push($line_chart_orders_last_year,0);
                }
                if ($order_payment[0]['payment'] != null){
                    $payment = $order_payment[0]['payment'];
                }
                if ($orders_sale[0]['sale'] != null){
                    $sale = $orders_sale[0]['sale'];
                }
                if ($orders_deal[0]['deal'] != null){
                    $deal = $orders_deal[0]['deal'];
                }
            }elseif ($_GET['date'] === 'year'){
//                echo "<pre>";
                $order_payment = Payments::find()->select('SUM(payment_sum) as payment')
                    ->where(['status' => '1'])
                    ->andWhere(['=', 'YEAR(pay_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $orders_sale = Orders::find()
                    ->select('SUM(total_price) as sale')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $orders_deal = Orders::find()
                    ->select('SUM(total_price) as deal')
                    ->where(['or',['status' => '1'],['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                $orders_cost = OrderItems::find()
                    ->select('(SUM(order_items.price) - SUM(order_items.cost)) as profit')
                    ->leftJoin('orders','orders.id = order_items.order_id')
                    ->where(['orders.status' => '3'])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y-m-d')])
                    ->groupBy('order_items.order_id')
                    ->asArray()
                    ->all();
                $cost = array_sum(array_column($orders_cost,'profit'));
                $total_order = Orders::find()
                    ->select('SUM(total_price) as total')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y-m-d')])
                    ->asArray()
                    ->all();
                if ($total_order[0]['total'] != null){
                    $chart_round_total = $total_order[0]['total'];
                }
                $chart_round_products = OrderItems::find()->select('SUM(order_items.price) as price,nomenclature.name')
                    ->leftJoin('orders','orders.id = order_items.order_id')
                    ->leftJoin('nomenclature', 'order_items.nom_id_for_name = nomenclature.id')
                    ->where(['or',['orders.status' => '2'],['orders.status' => '3']])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('order_items.nom_id_for_name')
                    ->asArray()
                    ->all();
                $round_chart_label = [];
                $round_chart_percent = [];
                if (!empty($chart_round_products)){
                    for ($i = 0; $i < count($chart_round_products); $i++){
                        array_push($round_chart_percent,round(($chart_round_products[$i]['price']/$chart_round_total)*100));
                        array_push($round_chart_label,$chart_round_products[$i]['name']);
                    }
                }else{
                    array_push($round_chart_percent,0);
                }
                $clients_payment = Orders::find()->select('clients.name, total_price')
                    ->leftJoin('clients', 'orders.clients_id = clients.id')
                    ->where(['orders.status' => '3'])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->asArray()
                    ->all();
                $line_chart_debt = Orders::find()->select('SUM(total_price) as debt, DATE(orders_date) as orders_date')
                    ->where(['orders.status' => '2'])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('MONTH(orders_date)')
                    ->asArray()
                    ->all();
                $line_chart_label = [];
                $line_chart_number = [];
                if (!empty($line_chart_debt)){
                    for ($k = 0; $k < count($line_chart_debt);$k++){
                        array_push($line_chart_number,$line_chart_debt[$k]['debt']);
                        array_push($line_chart_label,substr($line_chart_debt[$k]['orders_date'],0,7));
                    }
                }else{
                    array_push($line_chart_number,0);
                    array_push($line_chart_label,date('Y-m'));
                }
                $line_chart_orders = Orders::find()->select('SUM(total_price) as orders,DATE(orders_date) as orders_date')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'YEAR(orders_date)', date('Y')])
                    ->groupBy('MONTH(orders_date)')
                    ->asArray()
                    ->all();
                $line_chart_orders_label = [];
                $line_chart_orders_this_year = [];
                if (!empty($line_chart_orders)){
                    $months = [];
                    for ($n = 1; $n <= date('m');$n++){
                        if ($n < 10) {
                            $monthData = 0 . $n;
                            array_push($months, $monthData);
                        } else {
                            $monthData = '' . $n;
                            array_push($months, $monthData);
                        }
                    }
                    foreach ($months as $month) {
                        $found = false;
                        foreach ($line_chart_orders as $row) {
                            if (substr($row["orders_date"],5,2) == $month) {
                                $line_chart_orders_label[] = date('Y-' . $month);;
                                $line_chart_orders_this_year[] = $row["orders"];
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $line_chart_orders_label[] = date('Y-' . $month);
                            $line_chart_orders_this_year[] = 0;
                        }
                    }
                }else{
                    array_push($line_chart_orders_this_year,0);
                    array_push($line_chart_orders_label,date('Y-m'));
                }
                $lastYear = date('Y-m-d',strtotime("-1 year"));
                $line_chart_orders_last =  Orders::find()->select('SUM(total_price) as orders,DATE(orders_date) as orders_date')
                    ->where(['or',['status' => '2'],['status' => '3']])
                    ->andWhere(['=', 'YEAR(orders_date)',$lastYear])
                    ->groupBy('MONTH(orders_date)')
                    ->asArray()
                    ->all();
                $line_chart_orders_last_year = [];
                if (!empty($line_chart_orders_last[0]['orders'])){
                    $lastYearMonths = [];
                    for ($n = 1; $n <= date('m');$n++){
                        if ($n < 10) {
                            $monthData = 0 . $n;
                            array_push($lastYearMonths, $monthData);
                        } else {
                            $monthData = '' . $n;
                            array_push($lastYearMonths, $monthData);
                        }
                    }
                    foreach ($lastYearMonths as $month) {
                        $found = false;
                        foreach ($line_chart_orders_last as $row) {
                            if (substr($row["orders_date"],5,2) == $month) {
                                $line_chart_orders_last_year[] = $row["orders"];
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $line_chart_orders_last_year[] = 0;
                        }
                    }
                }else{
                    array_push($line_chart_orders_last_year,0);
                }
                if ($order_payment[0]['payment'] != null){
                    $payment = floor($order_payment[0]['payment']);
                }
                if ($orders_sale[0]['sale'] != null){
                    $sale = floor($orders_sale[0]['sale']);
                }
                if ($orders_deal[0]['deal'] != null){
                    $deal = floor($orders_deal[0]['deal']);
                }



            }
        }elseif (!isset($_GET['date']) && isset($_GET['start_date']) && isset($_GET['end_date'])){
            var_dump(56464);
        }
        $sub_page = [];
        $date_tab = [
            ['name' => 'Օրական','address' => '/dashboard/index?date=day'],
            ['name' => 'Ամսական','address' => '/dashboard/index?date=monthly'],
            ['name' => 'Տարեկան','address' => '/dashboard/index?date=year'],
        ];
        return $this->render('index',[
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'payment' => $payment,
            'sale' => $sale,
            'deal' => $deal,
            'cost' => $cost,
            'chart_round_total' => $chart_round_total,
            'chart_round_products' => $chart_round_products,
            'round_chart_label' => $round_chart_label,
            'round_chart_percent' => $round_chart_percent,
            'clients_payment' => $clients_payment,
            'line_chart_label' => $line_chart_label,
            'line_chart_number' => $line_chart_number,
            'line_chart_orders_label' => $line_chart_orders_label,
            'line_chart_orders_this_year' => $line_chart_orders_this_year,
            'line_chart_orders_last_year' => $line_chart_orders_last_year
        ]);
    }
}