<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $warehouse_id
 * @property int $nomenclature_id
 * @property int $count
 * @property float $price
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'nomenclature_id', 'count', 'price'], 'required'],
            [['warehouse_id', 'nomenclature_id', 'document_id'], 'integer'],
            [['price', 'count'], 'number'],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Պահեստ',
            'nomenclature_id' => 'Անվանակարգ',
            'count' => 'Քանակ',
            'price' => 'Գին',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }

    public function getWarehouseName()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    public function getNomenclatureName()
    {
        return $this->hasOne(Nomenclature::className(), ['id' => 'nomenclature_id']);
    }

    public function getDefaultTitle()
    {
        return CustomfieldsBlocksTitle::findOne(['id' => 10]);
    }

    public static function getDiscount($data)
    {
        if (!empty($data)) {
            $client_id = $data['client_id'];
//            $product_id = $data['prod_id'];
            $nom_id = $data['nom_id'];
            $orders_date = $data['orders_date'];
            $name = $data['name'];
//            $orders_price = $data['orders_price'];
            $warehouse_id = $data['warehouse_id'];
            $orders_count = $data['orders_count'];
            $orders_cost = $data['orders_cost'];
            $orders_total_count = $data['orders_total_count'];
            $orders_total_sum = $data['orders_total_sum'];
            $discount_desc = [];
            $discount_client_id_check = [];
            $res = [];
            $discount = Discount::find()
                ->with([
                    'clients' => function ($query) use ($client_id) {
                        $query->andFilterWhere(['discount_clients.client_id' => $client_id]);
                    },
                    'nomenclatures' => function ($query) use ($nom_id) {
                        $query->andFilterWhere(['discount_products.product_id' => $nom_id]);
                    }
                ])
                ->with(['clients', 'nomenclatures'])
                ->where(['discount.status' => 1])
                ->andWhere(['or',
                    ['<=', 'discount.start_date', $orders_date],
                    ['discount.start_date' => null]
                ])
                ->andWhere(['or',
                    ['>=', 'discount.end_date', $orders_date],
                    ['discount.end_date' => null]
                ])
                ->orderBy(['discount.discount_sortable' => SORT_ASC])
                ->asArray()
                ->all();
            $first_product = Products::find()
                ->where(['and',['nomenclature_id' => $nom_id],
                    ['warehouse_id' => $warehouse_id]])
                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                ->andWhere(['status' => '1'])
                ->andWhere(['!=', 'count_balance', 0])
                ->all();
            $end_result = [];
            $bal = $orders_count;

            foreach ($first_product as $item){
                if ($item->count_balance - $bal >= 0) {
                    $count_balance = $item->count_balance - $bal;
                    if ($discount) {
                        $desc = [];
                        $arr = [];
                        $count = 0;
                        $count_discount_id = '';
                        $price = $item->price;
                        for ($j = 0; $j < count($discount); $j++) {
                            if ($discount[$j]['discount_available_type'] == 3 && (!empty($discount[$j]['nomenclatures']) && !empty($discount[$j]['clients']))) { //client + product isset
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                }
                                elseif ($discount[$j]['discount_option'] == 2) {//bazmaki
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                            if ($discount[$j]['discount_available_type'] == 2  && (!empty($discount[$j]['nomenclatures']) && empty($discount[$j]['clients']))) {//for prod
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                } else {
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                            if ($discount[$j]['discount_available_type'] == 1 && (empty($discount[$j]['nomenclatures']) && !empty($discount[$j]['clients']))) {//for client
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                }
                                else {
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                            if ($discount[$j]['discount_available_type'] == 4 && (empty($discount[$j]['nomenclatures']) && empty($discount[$j]['clients']))) {
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                }
                                else {
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                        }
                        $discount_name = Discount::find()->select('id,name,discount,type')->asArray()->all();
                        $res['nomenclature_id'] = $nom_id;
                        $res['name'] = $name;
                        $res['cost'] = $orders_cost;
                        $res['product_id'] = $item->id;
                        $res['discount_name'] = $discount_name;
                        $res['discount_desc'] = $discount_desc;
                        $res['discount_client_id_check'] = $discount_client_id_check;
                        $res['price'] = $price;
                        $res['count'] = $bal;
                        $res['discount'] = $item->price - $price;//gin - zexchvac gin
                        $res['aah'] = $item->AAH;
                        if ($count_discount_id == ''){
                            $res['count_discount_id'] = 'չկա';
                        }else{
                            $res['count_discount_id'] = substr($count_discount_id,0,-1);
                        }
                        $res['format_before_price'] = $item->price;
                        $res['count_balance'] = $count_balance;
                        array_push($end_result,$res);

                            return json_encode($end_result);
                    }
                    else{
                        $desc = 'empty';
                        array_push($discount_desc, $desc);
                        $discount_client_id = 'empty';
                        array_push($discount_client_id_check,$discount_client_id);
                        $res['nomenclature_id'] = $nom_id;
                        $res['name'] = $name;
                        $res['cost'] = $orders_cost;
                        $res['product_id'] = $item->id;
                        $res['discount_name'] = 'empty';
                        $res['discount_desc'] = $discount_desc;
                        $res['discount_client_id_check'] = $discount_client_id_check;
                        $res['price'] = $item->price;
                        $res['count'] = $bal;
                        $res['discount'] = $item->price - $item->price;//gin - zexchvac gin
                        $res['aah'] = $item->AAH;
                        $res['count_discount_id'] = 'չկա';
                        $res['format_before_price'] = $item->price;
                        $res['count_balance'] = $count_balance;
                        array_push($end_result,$res);
                        return json_encode($end_result);

                    }
                }else{
                    if ($discount) {
                        $desc = [];
                        $arr = [];
                        $count = 0;
                        $count_discount_id = '';
                        $price = $item->price;
                        for ($j = 0; $j < count($discount); $j++) {
                            if ($discount[$j]['discount_available_type'] == 3 && (!empty($discount[$j]['nomenclatures']) && !empty($discount[$j]['clients']))) { //client + product isset
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                }
                                elseif ($discount[$j]['discount_option'] == 2) {//bazmaki
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                            if ($discount[$j]['discount_available_type'] == 2  && (!empty($discount[$j]['nomenclatures']) && empty($discount[$j]['clients']))) {//for prod
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                } else {
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                            if ($discount[$j]['discount_available_type'] == 1 && (empty($discount[$j]['nomenclatures']) && !empty($discount[$j]['clients']))) {//for client
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                }
                                else {
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                            if ($discount[$j]['discount_available_type'] == 4 && (empty($discount[$j]['nomenclatures']) && empty($discount[$j]['clients']))) {
                                if ($discount[$j]['discount_option'] == 1) {
                                    $check_client_id = Discount::findOne($discount[$j]['id']);
                                    if (!empty($check_client_id['discount_option_check_client_id'])) {
                                        $arr = explode(',', $check_client_id['discount_option_check_client_id']);
                                        if (!in_array($client_id, $arr)) {
                                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            } elseif (empty($discount[$j]['discount_filter_type'])) {
                                                if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                    $count++;
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                } elseif ($discount[$j]['discount_check'] == 1) {
                                                    if ($discount[$j]['type'] == 'percent') {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                    } else {
                                                        $count_discount_id .= $discount[$j]['id'] . ',';
                                                        $price = $price - $discount[$j]['discount'];
                                                    }
                                                }
                                                $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                            }
                                            array_push($arr, $client_id);
                                        }else{
                                            $desc = 'empty';
                                        }
                                    } else {
                                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        } elseif (empty($discount[$j]['discount_filter_type'])) {
                                            if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                                $count++;
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            } elseif ($discount[$j]['discount_check'] == 1) {
                                                if ($discount[$j]['type'] == 'percent') {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - ($price * $discount[$j]['discount']) / 100;
                                                } else {
                                                    $count_discount_id .= $discount[$j]['id'] . ',';
                                                    $price = $price - $discount[$j]['discount'];
                                                }
                                            }
                                            $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                        }
                                        array_push($arr, $client_id);
                                    }
                                    $uniq = array_unique($arr);
                                    $string_row = implode(',', $uniq);
                                    $discount_client_id = ['id' => $discount[$j]['id'], 'clients_id' => $string_row];
                                }
                                else {
                                    if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] <= $orders_total_count && $discount[$j]['max'] >= $orders_total_count) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] <= $orders_total_sum && $discount[$j]['max'] >= $orders_total_sum) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    } elseif (empty($discount[$j]['discount_filter_type'])) {
                                        if ($discount[$j]['discount_check'] == 0 && $count == 0) {
                                            $count++;
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        } elseif ($discount[$j]['discount_check'] == 1) {
                                            if ($discount[$j]['type'] == 'percent') {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - ($price * $discount[$j]['discount']) / 100;
                                            } else {
                                                $count_discount_id .= $discount[$j]['id'] . ',';
                                                $price = $price - $discount[$j]['discount'];
                                            }
                                        }
                                        $desc = ['id' => $discount[$j]['id'], 'name' => $discount[$j]['name'], 'discount' => $discount[$j]['discount'], 'type' => $discount[$j]['type']];
                                    }
                                    $discount_client_id = 'empty';
                                }
                                array_push($discount_client_id_check,$discount_client_id);
                                array_push($discount_desc, $desc);
                            }
                        }
                        $discount_name = Discount::find()->select('id,name,discount,type')->asArray()->all();
                        $res['nomenclature_id'] = $nom_id;
                        $res['name'] = $name;
                        $res['cost'] = $orders_cost;
                        $res['product_id'] = $item->id;
                        $res['discount_name'] = $discount_name;
                        $res['discount_desc'] = $discount_desc;
                        $res['discount_client_id_check'] = $discount_client_id_check;
                        $res['price'] = $price;
                        $res['count'] = $item->count_balance;
                        $res['discount'] = $item->price - $price;//gin - zexchvac gin
                        $res['aah'] = $item->AAH;
                        if ($count_discount_id == ''){
                            $res['count_discount_id'] = 'չկա';
                        }else{
                            $res['count_discount_id'] = substr($count_discount_id,0,-1);
                        }
                        $res['format_before_price'] = $item->price;
                        $count_balance = 0;
                        $bal -= $item->count_balance;
                        $res['count_balance'] = $count_balance;

                        array_push($end_result,$res);

//                        $item->count_balance = 0;
//                        $item->save(false);
                    }
                    else{
                        $desc = 'empty';
                        array_push($discount_desc, $desc);
                        $discount_client_id = 'empty';
                        array_push($discount_client_id_check,$discount_client_id);
                        $res['nomenclature_id'] = $nom_id;
                        $res['name'] = $name;
                        $res['cost'] = $orders_cost;
                        $res['product_id'] = $item->id;
                        $res['discount_name'] = 'empty';
                        $res['discount_desc'] = $discount_desc;
                        $res['discount_client_id_check'] = $discount_client_id_check;
                        $res['price'] = $item->price;
                        $res['count'] = $item->count_balance;
                        $res['discount'] = $item->price - $item->price;//gin - zexchvac gin
                        $res['aah'] = $item->AAH;
                        $res['count_discount_id'] = 'չկա';
                        $res['format_before_price'] = $item->price;
                        $count_balance = 0;
                        $bal -= $item->count_balance;
                        $res['count_balance'] = $count_balance;
                        array_push($end_result,$res);
                    }
                }
            }
        }
    }
}