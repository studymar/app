<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;

class PageFilterForm extends Model {
   
   public $searchstring;
   public $pageno;
   public $pageSize;

   public function __construct() {
      
   }

   public function rules() {
      return array(
         ['searchstring', 'string' ,'length'=>[1,50], 'tooShort'=>'{attribute} muss mind. {min} Zeichen lang sein.', 'tooLong'=>'{attribute} darf max. {max} Zeichen lang sein.' ],
         ['searchstring', 'match', 'pattern'=>'/^([a-zA-Z0-9öÖüÜäÄß?= &%:-{+}]+)/','message'=>'{attribute} enthält unerlaubte Zeichen.'],
         [['pageno','pageSize'], 'number'],
         ['pageno', 'default', 'value' => 0],
         ['pageSize', 'default', 'value' => 20],
        );
   }

   public function attributeLabels(){
      return array(
         'searchstring' => 'Suche',
      );
   }    

   
}