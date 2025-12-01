<?php

namespace backend\controllers;

use common\models\Apple;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Site controller
 */
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
        $apple = new Apple();
        echo $apple->color; // green

        //$apple->eat(50); // Бросить исключение - Съесть нельзя, яблоко на дереве
        echo $apple->size; // 1 - decimal

        $apple->fallToGround(); // упасть на землю
        $apple->eat(25); // откусить четверть яблока
        echo $apple->size; // 0,75

        /*
          $apples = Apple::find()->all();

      return $this->render('index', [
          'apples' => $apples,
      ]);
        */
    }

}
