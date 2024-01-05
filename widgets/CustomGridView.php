<?php

namespace app\widgets;

use yii\grid\GridView as BaseGridView;

class CustomGridView extends BaseGridView
{
      public $tableOptions = ['class' => 'table'];
  
      public $options = ['class' => 'table-responsive text-nowrap'];
}

?>