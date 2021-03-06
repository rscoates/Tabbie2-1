<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;

use api\models\Tournament;
use Yii;
use api\models\User;
use frontend\models\CheckinForm;
use common\models;
use common\models\LoginForm;
use yii\data\ActiveDataProvider;
use jakobreiter\quaggajs\BarcodeFactory;

/**
 * Class UserController
 * @package api\controllers
 */
class UserController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\User';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['index'], $actions['create'], $actions['update']);

		return $actions;
	}

	/**
	 * Returns the self Identity
	 * @return null|static
	 */
	public function actionMe()
	{
		return $this->redirect(["user/view", "id" => Yii::$app->user->id]);
	}

	/**
	 * @param null $tournament_id
     * @param null $user_id
	 * @return array
	 */
	public function actionGettournamentrole($user_id = null, $tournament_id = null)
	{
	    if ($user_id != null and $tournament_id != null) {
            $tournament = Tournament::find()
                ->where(["id" => $tournament_id])
                ->one();

            return [
                "tournamentId" => $tournament_id,
                "userId" => $user_id,
                "role" => $tournament->user_role_string((int) $user_id)
            ];
        } else {
	        return [];
        }
	}

	/**
	 * @param null $tournament_id
     * @param null $user_id
	 * @return array
	 */
	public function actionGeneratebarcode($user_id = null, $tournament_id = null)
    {
        $adjuText = Yii::t("app", "Adjudicator");

        $adju = models\Adjudicator::find()
            ->tournament($tournament_id)
            ->andWhere(["user_id" => $user_id])
            ->one();

        if ($adju instanceof models\Adjudicator) {
            $person = $this->cPerson(
                $adju->user->name,
                $adjuText,
                CheckinForm::ADJU . "-" . $adju->id,
                $adju->society->fullname
            );
        } else {
            $team = models\Team::find()
                ->tournament($tournament_id)
                ->andWhere("speakerA_id = $user_id OR speakerB_id = $user_id")
                ->one();

            if ($team instanceof models\Team) {
                if ($team->speakerA_id == $user_id) {
                    $person = $this->cPerson(
                        $team->speakerA->name,
                        $team->name,
                        CheckinForm::TEAMA . "-" . $team->id,
                        $team->society->fullname
                    );
                } else {
                    $person = $this->cPerson(
                        $team->speakerB->name,
                        $team->name,
                        CheckinForm::TEAMB . "-" . $team->id,
                        $team->society->fullname
                    );
                }
            } else {
                return [];
            }
        }

        ob_start();
        BarcodeFactory::generate($person['id'], $person['id'] . " " . $person['label']);

        $buffer = ob_get_clean();
        return ['b64' => base64_encode($buffer)];
	}

	public function cPerson($name, $extra, $code, $society)
	{
		return [
			"label"    => $name,
			"extra"   => $extra,
			"id"    => $code,
			"society" => $society,
		];
	}
}