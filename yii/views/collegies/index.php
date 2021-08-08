<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
    <h1>Collegies</h1>
    <ul>
        <?php foreach ($collegies as $row): ?>
            <li>
                <?= Html::encode("{$row->college_id} {$row->college_name} {$row->college->phone}") ?>:
            </li>
        <?php endforeach; ?>
    </ul>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

