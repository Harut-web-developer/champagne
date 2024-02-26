<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

$this->title = $name;
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="site-error container">
    <img src="/upload/pngwing(6).png" alt="404 image" style="width: 50%;">
<!--    <div class="top-left">Օ՜փս, կարծես ինչ-որ բան սխալ է տեղի ունեցել։</div>-->
</div>

<style>
    .container {
        position: relative;
        text-align: center;
        color: white;
        font-style: italic;
        font-size: 2vw;
        text-shadow: 1px 1px #dd1f1f;
    }
    .top-left {
        position: absolute;
        top: 8px;
        left: 16px;
        text-decoration: overline;
        color: black;
    }
</style>
