<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\YarnColor;

/**
 * YarnColorSearch represents the model behind the search form of `app\models\YarnColor`.
 */
class YarnColorSearch extends YarnColor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'yarn_kind_id'], 'integer'],
            [['color_hex', 'color_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = YarnColor::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'yarn_kind_id' => $this->yarn_kind_id,
        ]);

        $query->andFilterWhere(['like', 'color_hex', $this->color_hex])
            ->andFilterWhere(['like', 'color_name', $this->color_name]);

        return $dataProvider;
    }
}
