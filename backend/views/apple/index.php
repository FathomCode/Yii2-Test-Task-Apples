<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Apple; // Подключаем модель Apple для доступа к константам статусов

/** @var yii\web\View $this */
/** @var common\models\Apple[] $apples */

$this->title = 'Управление Яблоками';
?>
    <div class="apple-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::beginForm(['/apple/generate'], 'post', ['style' => 'display:inline-block; margin-right: 10px;']) ?>
        <?= Html::submitButton('Сгенерировать случайные яблоки', ['class' => 'btn btn-success']) ?>
        <?= Html::endForm() ?>
    </p>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Цвет</th>
                    <th>Появилось</th>
                    <th>Упало</th>
                    <th>Статус</th>
                    <th>Съедено (%)</th>
                    <th>Осталось (%)</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($apples)): ?>
                    <tr>
                        <td colspan="8">Нет яблок для отображения.</td>
                    </tr>
                <?php else: ?>
                <?php foreach ($apples as $apple): ?>
                <?php $isRotten = $apple->isRotten(); // проверка на гниль ?>
                <tr>
                    <td><?= $apple->id ?></td>
                    <td><?= Html::encode($apple->color) ?></td>
                    <td><?= date('d.m.Y H:i', $apple->created_at) ?></td>
                    <td><?= $apple->fell_at ? date('d.m.Y H:i', $apple->fell_at) : '—' ?></td>
                    <td style="text-align:">
                              <span class="status
                                  <?= $isRotten ? 'bg-danger' :
                                  ($apple->status === Apple::STATUS_FALLEN ? 'bg-warning' : 'bg-success')
                              ?>">
                                  <?= Html::encode($apple->getStatusText()) ?>
                              </span>
                    </td>
                    <td><?= $apple->eaten ?>%</td>
                    <td><?= (100 - $apple->eaten) ?>% (<?= $apple->getSize() ?>)</td>
                    <td>
                        <?php if ($apple->status === Apple::STATUS_ON_TREE): ?>
                            <?= Html::beginForm(['/apple/fall', 'id' => $apple->id], 'post', ['style' => 'display:inline-block; margin-right: 5px;']) ?>
                            <?= Html::submitButton('Уронить', ['class' => 'btn btn-primary btn-sm']) ?>
                            <?= Html::endForm() ?>
                        <?php endif; ?>

                        <?php if ($apple->status === Apple::STATUS_FALLEN && !$isRotten && $apple->eaten < 100): ?>
                        <?= Html::beginForm(['/apple/eat', 'id' => $apple->id], 'post', ['style' => 'display:inline-block; margin-right: 5px;']) ?>
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="number" name="percent" class="form-control" placeholder="%" min="1" max="<?= 100 - $apple->eaten ?>" required>
                            <button type="submit" class="btn btn-warning btn-sm">Съесть</button>
                        </div>
                            <?= Html::endForm() ?>
                        <?php endif; ?>

                        <?php if ($apple->isRotten() && false): // сгнило; Disable delete function ?>
                            <?= Html::beginForm(['/apple/delete', 'id' => $apple->id], 'post', ['style' => 'display:inline-block; margin-right: 5px;']) ?>
                            <?= Html::submitButton('Удалить (сгнило)', ['class' => 'btn btn-danger btn-sm', 'data' => ['confirm' => 'Вы уверены, что хотите удалить это яблоко?']]) ?>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                    </td>
                </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

<style>

    table td:has(.status) {
        text-align: center;
    }
    .status {
        padding: 2px 9px 4px;
        color: #fff;
        text-align: center;
        border-radius: 30px;
    }
    .bg-success { background-color: #28a745 !important; } /* На дереве */
    .bg-warning { background-color: #ffc107 !important; color: #212529 !important; } /* Упало */
    .bg-danger { background-color: #dc3545 !important; } /* Гнилое */
</style>