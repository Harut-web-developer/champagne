<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;
$this->title = 'My Yii Application';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="site-error container">
    <div class="top_left_error">Այս էջում ունեք տվյալների սահմանափակում։</div>
</div>

<style>
    .container {
        text-align: center;
        color: white;
        font-style: italic;
        font-size: 2vw;
        text-shadow: 1px 1px #dd1f1f;
    }
    @media screen and (max-width: 424px) {
        .top_left_error{
            font-size:3em;
            top: 8px;
            left: 16px;
            text-decoration: underline;
            color: black;
        }
    }
    @media screen and (min-width: 425px) and (max-width: 1199px) {
        .top_left_error{
            font-size:1.5em;
            top: 8px;
            left: 16px;
            text-decoration: underline;
            color: black;
        }
    }
    @media screen and (min-width: 1200px) {
        .top_left_error{
            font-size:0.8em;
            top: 8px;
            left: 16px;
            text-decoration: underline;
            color: black;
        }
    }
</style>
