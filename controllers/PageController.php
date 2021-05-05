<?php

namespace app\controllers;

use Yii;
use app\models\filters\MyAccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class PageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Ruft eine beliebige Contentseite auf
     * @param string $p [optional] Urlname der Seite. Wenn leer dann Startseite.
     */
    public function actionIndex()
    {
        return $this->run('voting/index');
        
        /*
        return $this->render('index', [
            
        ]);
        */  
    }
    
    
   
    
}



