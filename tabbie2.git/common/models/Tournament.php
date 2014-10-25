<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tournament".
 *
 * @property integer $id
 * @property integer $convenor_user_id
 * @property integer $tabmaster_user_id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property resource $logo
 * @property string $time
 *
 * @property Adjudicator[] $adjudicators
 * @property DrawAfterRound[] $drawAfterRounds
 * @property Round[] $rounds
 * @property Team[] $teams
 * @property User $convenorUser
 * @property User $tabmasterUser
 * @property TournamentHasQuestions[] $tournamentHasQuestions
 * @property Questions[] $questions
 * @property Venue[] $venues
 */
class Tournament extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tournament';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['convenor_user_id', 'tabmaster_user_id', 'name', 'start_date', 'end_date'], 'required'],
            [['convenor_user_id', 'tabmaster_user_id'], 'integer'],
            [['start_date', 'end_date', 'time'], 'safe'],
            [['logo'], 'file'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'Tournament ID'),
            'convenor_user_id' => Yii::t('app', 'Convenor'),
            'tabmaster_user_id' => Yii::t('app', 'Tabmaster'),
            'name' => Yii::t('app', 'Name'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'logo' => Yii::t('app', 'Logo'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    public function getFullname() {
        return $this->name . " " . substr($this->start_date, 0, 4);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicators() {
        return $this->hasMany(Adjudicator::className(), ['tournament_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrawAfterRounds() {
        return $this->hasMany(DrawAfterRound::className(), ['tournament_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRounds() {
        return $this->hasMany(Round::className(), ['tournament_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams() {
        return $this->hasMany(Team::className(), ['tournament_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConvenorUser() {
        return $this->hasOne(User::className(), ['id' => 'convenor_user_id']);
    }

    /**
     * Returns a list of Tabmasters
     * @return type
     */
    public function getTabmasterOptions() {
        return \yii\helpers\ArrayHelper::map(User::find()->where("role>10")->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTabmasterUser() {
        return $this->hasOne(User::className(), ['id' => 'tabmaster_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournamentHasQuestions() {
        return $this->hasMany(TournamentHasQuestions::className(), ['tournament_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions() {
        return $this->hasMany(Questions::className(), ['id' => 'questions_id'])->viaTable('tournament_has_questions', ['tournament_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenues() {
        return $this->hasMany(Venue::className(), ['tournament_id' => 'id']);
    }

}
