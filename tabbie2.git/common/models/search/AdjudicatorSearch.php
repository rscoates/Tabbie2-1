<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Adjudicator;

/**
 * AdjudicatorSearch represents the model behind the search form about `common\models\Adjudicator`.
 */
class AdjudicatorSearch extends Adjudicator {

    public $tournament_id;
    public $name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['id', 'integer'],
            ['strength', 'safe'],
            ['name', 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Adjudicator::find()->joinWith("user")->where(["tournament_id" => $this->tournament_id]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'strength',
                'name',
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'strength' => $this->strength,
        ]);
        $query->andFilterWhere(["like", "CONCAT(user.givenname, ' ', user.surename)", $this->name]);

        return $dataProvider;
    }

    public static function getSearchArray($tid) {
        $adjudicators = Adjudicator::find()->joinWith("user")->where(["tournament_id" => $tid])->all();
        foreach ($adjudicators as $a) {
            $filter[$a->name] = $a->name;
        }
        return $filter;
    }

}