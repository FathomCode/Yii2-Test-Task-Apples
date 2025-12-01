<?php

namespace backend\controllers;

use common\models\Apple;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;


class AppleController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'generate', 'fall', 'eat', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /*
        $apple = new Apple();
        echo $apple->color; // green

        //$apple->eat(50); // Бросить исключение - Съесть нельзя, яблоко на дереве
        echo $apple->size; // 1 - decimal

        $apple->fallToGround(); // упасть на землю
        $apple->eat(25); // откусить четверть яблока
        echo $apple->size; // 0,75
        */


        $apples = Apple::find()->all();

        return $this->render('index', [
          'apples' => $apples,
        ]);

    }

    public function actionGenerate()
    {
        if (Yii::$app->request->isPost) {
            $count = rand(1, 5);
            $generatedCount = 0;
            for ($i = 0; $i < $count; $i++) {
                $apple = new Apple();
                if ($apple->save()) {
                    $generatedCount++;
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось сгенерировать яблоко: ' . print_r($apple->errors, true));
                }
            }
            if ($generatedCount > 0) {
                Yii::$app->session->setFlash('success', "Сгенерировано {$generatedCount} новых яблок.");
            }
        }
        return $this->redirect(['index']);
    }


    public function actionFall($id)
    {
        $apple = Apple::findOne($id);

        try {
            $apple->fallToGround();
            Yii::$app->session->setFlash('success', 'Яблоко успешно упало.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionEat($id)
    {
        $apple = Apple::findOne($id);
        $percent = Yii::$app->request->post('percent');


        if (!is_numeric($percent) || $percent < 1 || $percent > 100) {
            Yii::$app->session->setFlash('error', 'Введите корректный процент от 1 до 100.');
            return $this->redirect(['index']);
        }

        try {
            if ($apple->eat($percent)) {
                Yii::$app->session->setFlash('success', 'Яблоко успешно съедено на ' . $percent . '%.');
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка при попытке съесть яблоко.');
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }


    public function actionDelete($id)
    {
        //Disabled
        return $this->redirect(['index']);

        $apple = Apple::findOne($id);
        if ($apple->delete()) {
            Yii::$app->session->setFlash('success', 'Яблоко успешно удалено.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить яблоко.');
        }

        return $this->redirect(['index']);
    }
}
