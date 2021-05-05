<?php

namespace app\models\voting;

use Yii;

/**
 * This is the model class for table "votingoption".
 *
 * @property int $id
 * @property string $value
 * @property int $votingquestion_id
 *
 * @property Votingquestion $votingquestion
 */
class Votingoption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'votingoption';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['votingquestion_id'], 'required'],
            [['votingquestion_id'], 'integer'],
            [['votingquestion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Votingquestion::className(), 'targetAttribute' => ['votingquestion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'votingquestion_id' => 'Votingquestion ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVotingquestion()
    {
        return $this->hasOne(Votingquestion::className(), ['id' => 'votingquestion_id']);
    }
}
