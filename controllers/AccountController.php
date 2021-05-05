<?php

namespace app\controllers;

use Yii;
use app\models\filters\MyAccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use app\models\content\Page;
use app\models\user\User;
use app\models\role\Right;
use \app\models\organisation\Organisation;
use app\models\forms\LoginForm;

class AccountController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => MyAccessControl::className(),
                //'only' => ['logout', 'myAccount'],
                //'except' => ['create', 'update'],
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['index','registrate','registrationSaved','registrationFinished','resetPassword'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['my-account','logout'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied by default
                ],
            ],            
        ];
    }

    /**
     * Zeigt die Loginseite
     */
    public function actionIndex()
    {
        try {
            $topError = null;

            $model = new LoginForm();
            if($model->load(Yii::$app->request->post()) && $model->login()){
                return $this->goHome();
            }
        } catch (Exception $e) {
            Yii::error('Login failed: '.$e, __METHOD__);
            $topError = Yii::t('errors', 'LOGINERROR');
        }
        
        return $this->render('index', [
            'model'     => $model,
            'topError'  => $topError,
            'errors'    => $model->getErrors(),
        ]);
        
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }    
    
    /**
     * Zeigt die Account-Seite im eingeloggten Bereich
     */
    public function actionMyAccount()
    {
        try {
            $topSuccess = null;
            $topError   = null;
            $topSuccessPassword = null;
            $topErrorPassword   = null;

            $model = new User();
            $model = $model->getUser();
            if($model){
                if($model->load(Yii::$app->request->post()) && $model->validate()){
                    $model->save();
                    $topSuccess = Yii::t('errors', 'DATASAVINGSUCCESS');;
                }
                $passwordModel = new \app\models\forms\ChangePasswordForm();
                if($passwordModel->load(Yii::$app->request->post()) && $passwordModel->validate()){
                    $model = $passwordModel->mapToUser($model);
                    $model->save();
                    $topSuccessPassword = Yii::t('errors', 'DATASAVINGSUCCESS');;
                }
            }
            else 
                throw new yii\web\ServerErrorHttpException(Yii::t('errors', 'DATALOADINGERROR'));
            
            
            $allOrganisations = Organisation::find()
            ->select(['name'])
            ->indexBy('id')
            ->column();

            return $this->render('my-account', [
                'model'             => $model,
                'allOrganisations'  => $allOrganisations,
                'topSuccess'        => $topSuccess,
                'topError'          => $topError,
                'passwordModel'     => $passwordModel,
                'topSuccessPassword'=> $topSuccessPassword,
                'topErrorPassword'  => $topErrorPassword,
                'errors'            => $model->getErrors(),
                'errorsPassword'    => $passwordModel->getErrors(),
            ]);            
            
        } catch (\yii\db\IntegrityException $e) {
            Yii::warning('Daten konnte nicht gespeichert werden ('.$model->username.'): '.$e, __METHOD__);
            $topError = Yii::t('errors', 'DATASAVINGERROR');
        } catch (\yii\base\ErrorException $e) {
            Yii::error('Error: '.$e, __METHOD__);
            $topError = Yii::t('errors', 'DATASAVINGERROR');
        } catch (Exception $e) {
            Yii::error('Saving MyData failed: '.$e, __METHOD__);
            $topError = Yii::t('errors', 'DATASAVINGERROR');
        }


                
    }

    /**
     * Zeigt die Account-Seite im eingeloggten Bereich
     */
    public function actionRegistrate()
    {
        try {
            $topError = null;

            $model = new User();
            $model->scenario = "registration";
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                if($model->createUser()){
                    $model->sendRegistrationMail ();
                    return $this->redirect(['account/registration-saved']);
                }
            }
            //var_dump($model->getErrors());
        } catch (\yii\base\ErrorException $e) {
            Yii::error('Error: '.$e, __METHOD__);
            throw new UnprocessableEntityHttpException($e->getMessage());
        } catch (Exception $e) {
            Yii::error('Registration failed: '.$e, __METHOD__);
            $topError = Yii::t('errors', 'LOGINERROR');
        } catch (\yii\db\IntegrityException $e) {
            Yii::warning('Neuer User nicht angelegt (User bereits voranden '.$model->username.'): '.$e, __METHOD__);
            $model->addError('username',Yii::t('errors', 'DUPLICATEDUSER'));
        }
        
        $allOrganisations = Organisation::find()
        ->select(['name'])
        ->indexBy('id')
        ->column();
        
        return $this->render('registrate', [
            'model'     => $model,
            'allOrganisations' => $allOrganisations,
            'topError'  => $topError,
            'errors'    => $model->getErrors(),
        ]);
        
    }

    /**
     * Zeigt Info nach Registrierung, dass Email verschickt
     */
    public function actionRegistrationSaved()
    {
        return $this->render('registration-sent', [
        ]);        
    }
    /**
     * Zeigt Registrierung finished-Info
     */
    public function actionRegistrationFinished($p)
    {
        //bereits eingeloggt? Dann zur Home
        if(!Yii::$app->user->isGuest){
            return $this->goHome();
        }

        try {
            //useraccount freischalten
            if( User::validateUser($p) ){
                return $this->render('registration-finished', [
                ]);        
            }
            //sonst einen Fehler ausgeben
            throw new UnprocessableEntityHttpException(Yii::t('errors','REGISTRATIONVALIDATEERROR'));
        } catch (\yii\base\ErrorException $e) {
            Yii::error('Error: '.$e, __METHOD__);
            throw new UnprocessableEntityHttpException($e->getMessage());
        } catch(\yii\base\Exception $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

    }
    
    /**
     * Passwort vergessen-Seite
     */
    public function actionResetPassword()
    {
        $topError = null;

        try {
            $model = new \app\models\forms\ResetPasswordForm();
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                $item = User::findByUsername($model->username);
                if($item->isvalidated){
                    $item->sendResetPasswordMail($item->resetPassword());
                    return $this->render('reset-password-sent', [
                    ]);
                }
            }
        } catch (Exception $e) {
            Yii::error('ResetPassword failed: '.$e, __METHOD__);
            throw new yii\web\ServerErrorHttpException('SERVERERROR');
        }
        
        return $this->render('reset-password', [
            'model'     => $model,
            'errors'    => $model->getErrors(),
        ]);

    }
    
    
}
