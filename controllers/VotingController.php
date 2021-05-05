<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\base\Exception;
use yii\web\UnprocessableEntityHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\data\ActiveDataProvider;
use app\models\Errormessages;
use app\models\filters\MyAccessControl;
use app\models\role\Right;
use app\models\FeatureFlags;
use app\models\voting\Votingtopic;
use app\models\voting\Votingquestion;
use app\models\voting\Votingtype;
use app\models\voting\Votinganswer;
use app\models\voting\Votingweights;
use app\models\voting\Votingoption;

class VotingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
        ];
    }

    public function beforeAction($action) 
    { 
        if(!FeatureFlags::VOTING_ACTIVATED) throw new NotFoundHttpException(Yii::t('errors', 'NOTSUPPORTED'));

        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }    
    
    /**
     * Displays List of Votingtopics
     *
     * @return Array
     */
    public function actionIndex()
    {
        return $this->render('index',[
        ]);
        
    }
    
    /**
     * Lädt die aktuell aktiven Votingtopics
     * @return JSON
     */
    public function actionGetTopics()
    {
        $this->layout = 'nolayout';

        try {
            $items = Votingtopic::getPublicVotingtopics();
            return $this->asJson($items);

        } catch (\yii\db\Exception $e){
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("Votingtopiclist konnte nicht geladen werden");
        } catch (Exception $e){
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("Votingtopiclist konnte nicht geladen werden");
        }

    }

    /**
     * Lädt alle Questions des Topics
     * @return JSON
     */
    public function actionGetQuestionsOfTopic($p, $p2 = false)
    {
        $this->layout = 'nolayout';

        $model      = Votingtopic::find()->where('id = '.$p)->one();

        try {
            $items = $model->getPublicVotingquestions()->with('votingoptions')->asArray()->all();
            if($p2){
                foreach($items as &$item){
                    $item['hasAnswered'] = Votingquestion::checkVWHasAnswered($item['id'],$p2);
                }
            }
            else {
                foreach($items as &$item){
                    $item['hasAnswered'] = Votingquestion::checkHasAnswered($item['id']);
                }
            }
            return $this->asJson(['items'=>$items]);

        } catch (\yii\db\Exception $e){
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("List of Votingquestions konnte nicht geladen werden");
        } catch (Exception $e){
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("List of Votingquestions konnte nicht geladen werden");
        }

    }

    
    /**
     * Lädt alle Antworten einer Gewichtung
     * @param int $p ID des Topics
     * @param int $p ID des Votingweight
     * @return JSON
     */
    public function actionGetAnswersOfVotingweight($p, $p2)
    {
        $this->layout = 'nolayout';


        try {
            $topic      = Votingtopic::find()->where('id = '.$p)->one();
            $vw         = Votingweights::find()->where('id = '.$p2)->andWhere('votingtopic_id = '.$topic->id)->one();
            
            return $this->asJson(['votinganswers'=>$vw->votinganswers]);

        } catch (\yii\db\Exception $e){
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("List of Votingquestions konnte nicht geladen werden");
        } catch (Exception $e){
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("List of Votingquestions konnte nicht geladen werden");
        }

    }
    
    
    /**
     * Setzt ein Voting zu einer Question
     * @param $p ID der question
     * @return array
     *
    public function actionVote($p)
    {
        try {
            $model = Votingquestion::find()->where('id = '.$p)->one();
            
            //prüfen, ob aktiv, sonst zur Startseite weiterleiten
            if(!$model->active)
                return $this->redirect(['voting/index']);
                

            return $this->render('vote',[
                'model' => $model,
            ]);

        } catch (\yii\db\Exception $e){
            throw new ServerErrorHttpException("Votingtopic konnte nicht geladen werden");
        } catch (Exception $e){
            throw new ServerErrorHttpException("Votingtopic konnte nicht geladen werden");
        }

    }


    /**
     * Ruft eine Umfrage und dessen Resultate eines Topics ab
     * @param $p ID des Topics
     * @param $p2 ID des Votingweights [optional] hierdurch werden die bisherigen Antworten des Gewichtungsusers rausgesucht,
     * statt der IP
     * @return array
     */
    public function actionGetVotingOfTopic($p,$p2 = false)
    {
        $this->layout = 'nolayout';

        try {
            $topic  = Votingtopic::find()
                    ->where('id = '.$p)
                    ->with('votingweights')
                    ->asArray()
                    ->one();
            
            if(!$topic["active"])
                throw new NotFoundHttpException(Yii::t('errors', 'NOTSUPPORTED'));
            
            $voting = Votingquestion::find()
                    ->where('votingtopic_id = '.$p)
                    ->andWhere('active = 1')
                    ->with('votingtype')
                    ->with('votingoptions');

            $votingObj = $voting->one();
            
            $myAnswers      = [];
            $countresults   = 0;
            $sumValues      = 0;
            $resultstatistics = [];
            if($votingObj){
                //wenn mit Gewichtung, bisherige Antworten des GewichtungsUsers heraussuchen
                if($p2){
                    $myAnswers      = Votinganswer::getAnswersOfVotingweights($votingObj,$p2);
                }
                //sonst die Antworten der aktuellen IP heraussuchen
                else
                    $myAnswers      = Votinganswer::getAnswersOfIp($votingObj);
                $countresults   = Votinganswer::countResultsByAnswerer($votingObj);
                //Stimmen zählen nach Anzahl Ergebnisse oder nach Stimmengewichtung
                if($votingObj->hasweighting && $votingObj->votingtype->name == "radio")
                    $sumValues      = Votinganswer::countResultsByStimmen($votingObj);
                else
                    $sumValues      = Votinganswer::countResultsByValues($votingObj);

                //results nur ausgeben, wenn erlaubt und schon abgestimmt
                if($votingObj->showresults && count($myAnswers)>0){
                    if($votingObj->votingtype->name == "text")
                        $resultstatistics = Votinganswer::getResultStatisticsPerValue($votingObj, $sumValues);
                    else if($votingObj->hasweighting && $votingObj->votingtype->name == "radio")
                        $resultstatistics = Votinganswer::getResultStatisticsPerOption($votingObj, $sumValues, true);
                    else
                        $resultstatistics = Votinganswer::getResultStatisticsPerOption($votingObj, $sumValues);
                }
            }
            
            return $this->asJson([
                'topic'=>$topic,
                'voting'=>$voting->asArray()->one(),
                'myanswers' => $myAnswers,
                'countresults' => $countresults,
                'resultstatistics' => $resultstatistics,
                'sumValues' => $sumValues,
            ]);

        } catch (\yii\db\Exception $e){
            Yii::error($e->getMessage(),__METHOD__);
            throw new ServerErrorHttpException("Voting konnte nicht geladen werden");
        } catch (Exception $e){
            Yii::error($e->getMessage(),__METHOD__);
            throw new ServerErrorHttpException("Voting konnte nicht geladen werden");
        }

    }

    /**
     * Speichert eine Umfrage
     * @param $p ID der Question
     * @return array
     */
    public function actionSaveVotingOfQuestion($p)
    {
        $this->layout = 'nolayout';

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $saved = false;
        try {
            $voting = Votingquestion::find()
                    ->where('id = '.$p)
                    ->andWhere('active = 1')
                    ->one();

            $model = new \app\models\forms\VoteForm();
            //Votinganswer::saveAnswers(Yii::$app->request->post('Votinganswers', []));
            
            //$answers            = Yii::$app->request->post('Votinganswer', []);
            //$votingweights_id   = Yii::$app->request->post('Votinganswer_votingweights_id', []);
            if($model->load(Yii::$app->request->post())){
                if($model->votingweights_id && $voting->hasweighting){
                    $votingweights      = Votingweights::find()->where('id = '.$model->votingweights_id)->one();
                    Votinganswer::createAnswers($voting, $model->votinganswer, $model->votingweights_id);
                    $votingweights->setActive();
                    $saved = true;
                }
                else {
                    Votinganswer::createAnswers($voting, $model->votinganswer);
                    $saved = true;
                }
                $transaction->commit();
            }
            else Yii::error ('Error while saving answer of question '.$voting->id.' / No Result found in Request.',__METHOD__);

            return $this->asJson([
                'saved' => $saved,
            ]);

        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("Voting konnte nicht gespeichert werden");
        } catch (Exception $e){
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            throw new ServerErrorHttpException("Voting konnte nicht gespeichert werden");
        }

    }
    
}
