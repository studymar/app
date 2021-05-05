<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;

class VoteForm extends Model {
   
   public $votinganswer;
   public $votingweights_id;

   public function __construct() {
      
   }

   
   public function rules() {
        return array(
            [['votinganswer'], 'required' ],
            [['votinganswer'], 'each', 'rule' => ['integer'], 'message'=>'{attribute} muss eine Zahl sein' ],
            [['votingweights_id'],'integer'],
        );
   }


   
}