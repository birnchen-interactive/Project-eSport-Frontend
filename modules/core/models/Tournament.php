<?php
/**
 * Created by PhpStorm.
 * User: Birnchen Studios
 * Date: 18.02.2019
 * Time: 09:54
 */

namespace app\modules\core\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class Tournament
 * @package app\modules\core\models
 *
 * @property int $tournament_id
 * @property int $game_id
 * @property int $mode_id
 * @property int $rules_id
 * @property int $bracket_id
 * @property int $cup_id
 * @property string $tournament_name
 * @property string $tournament_description
 * @property string $dt_starting_time
 * @property string $dt_register_begin
 * @property string $dt_register_end
 * @property string $dt_checkin_begin
 * @property string $dt_checkin_ends
 * @property bool $has_password
 * @property string $password
 */
class Tournament extends ActiveRecord
{
    /**
     * @return array the attribute labels
     */
    public function attributeLabels()
    {
        return [
            'tournament_id' => Yii::t('app', 'tournament id'),
            'game_id' => Yii::t('app', 'game id'),
            'mode_id' => Yii::t('app', 'mode id'),
            'rules_id' => Yii::t('app', 'rules id'),
            'bracket_id' => Yii::t('app', 'bracket id'),
            'tournament_name' => Yii::t('app', 'tournament name'),
            'tournament_description' => Yii::t('app', 'tournament description'),
            'dt_starting_time' => Yii::t('app', 'dt starting time'),
            'dt_register_begin' => Yii::t('app', 'dt register begin'),
            'dt_register_end' => Yii::t('app', 'dt register end'),
            'dt_checkin_begin' => Yii::t('app', 'dt checkin begin'),
            'dt_checkin_ends' => Yii::t('app', 'dt checkin end'),
            'has_password' => Yii::t('app', 'has password'),
            'password' => Yii::t('app', 'password')

        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'tournaments';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->tournament_id;
    }

    /**
     * @return int
     */
    public function getGameId()
    {
        return $this->game_id;
    }

    /**
     * @return int
     */
    public function getModeId()
    {
        return $this->mode_id;
    }

    /**
     * @return int
     */
    public function getRulesId()
    {
        return $this->rules_id;
    }

    /**
     * Get Rules Name
     */
    public function getRules()
    {
        $baseRuleSet = $this->getBaseRuleSet()->one();
        $subRuleSet = $this->getSubRuleSet()->all();

        $rulesName = [
            'baseSet' => $baseRuleSet->getRulesName(),
            'subRulesSet' => $subRuleSet,
        ];

        return $rulesName;
    }

    /**
     * @return int
     */
    public function getBracketId()
    {
        return $this->bracket_id;
    }

    /**
     * @return int
     */
    public function getCupId()
    {
        return $this->cup_id;
    }

    /**
     * @return string
     */
    public function getTournamentName()
    {
        return $this->tournament_name;
    }

    /**
     * @return string
     */
    public function showRealTournamentName()
    {

        $cup = $this->getCup()->one();
        $tMode = $this->getMode()->one();

        $cupName = $cup->getName();
        $season = 'S' . $cup->getSeason();

        $modeName = $tMode->getName();

        $dayName = $this->getTournamentName();

        return $cupName . ' ' . $season . ' ' . $modeName . ' ' . $dayName;
    }

    /**
     * @return string
     */
    public function getTournamentDescription()
    {
        return $this->tournament_description;
    }

    /**
     * @return string
     */
    public function getDtStartingTime()
    {
        return $this->dt_starting_time;
    }

    /**
     * @return string
     */
    public function getDtRegisterBegin()
    {
        return $this->dt_register_begin;
    }

    /**
     * @return string
     */
    public function getDtRegisterEnd()
    {
        return $this->dt_register_end;
    }

    /**
     * @return string
     */
    public function getDtCheckinBegin()
    {
        return $this->dt_checkin_begin;
    }

    /**
     * @return string
     */
    public function getDtCheckinEnd()
    {
        return $this->dt_checkin_ends;
    }

    /**
     * @return bool
     */
    public function getHasPassword()
    {
        return $this->has_password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Finds user by username.
     *
     * @param string $tournamentname the name
     * @return static|null the tournament, if a tournament with that tournament name exists
     */
    public static function findByTournamentName($tournamentname)
    {
        return static::findOne(['tournament_name' => $tournamentname]);
    }

    public static function getTournamentById($tournamentId)
    {
        return static::findOne(['tournament_id' => $tournamentId]);
    }

    /**
     * @param $subTeams
     * @param $user
     * @return bool
     */
    public function showRegisterBtn($subTeams, $user)
    {
        if ($this->getMode()->one()->getMainPlayer() == 1) {
            return (NULL === $user) ? false : true;
        }

        if (count($subTeams) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $subTeams
     * @param $user
     * @return bool
     */
    public function showCheckInBtn($subTeams, $user)
    {
        if ($this->getMode()->one()->getMainPlayer() == 1) {
            
            if (NULL === $user) {
                return false;
            }

            $isParticipating = $this->checkPlayerParticipating($user);
            return ($isParticipating) ? true : false;
        }

        return true;
    }

    /**
     * @param $subTeams
     * @param $user
     * @return array
     */
    public function getRegisterBtns($subTeams, $user)
    {
        if ($this->getMode()->one()->getMainPlayer() == 1) {

            $isParticipating = $this->checkPlayerParticipating($user);

            $btnValue = ($isParticipating) ? 'Abmelden' : 'Registrieren';
            $btnColor = ($isParticipating) ? 'btn-danger' : 'btn-success';

            $tmp = array(
                array(
                    'type' => 'user',
                    'id' => $user->getId(),
                    'name' => Html::tag('span', $user->getUsername()),
                    'btn' => Html::submitInput($btnValue, ['class' => 'btn ' . $btnColor, 'name' => 'submitText']),
                ),
            );
            return $tmp;
        }

        $retArr = array();
        foreach ($subTeams as $key => $subTeam) {

            if ($subTeam->getTournamentModeId() !== $this->getModeId()) {
                continue;
            }

            $modeMainPlayers = $this->getMode()->one()->getMainPlayer();

            $mainFound = 0;
            $teamMembers = SubTeamMember::getTeamMembers($subTeam->getId());
            foreach ($teamMembers as $teamMemberKey => $teamMember) {
                if ($teamMember->getIsSubstitute() === 0) {
                    $mainFound++;
                }
            }

            if ($mainFound < $modeMainPlayers) {
                continue;
            }

            $isParticipating = $this->checkTeamParticipating($subTeam);

            $btnValue = ($isParticipating) ? 'Abmelden' : 'Registrieren';
            $btnColor = ($isParticipating) ? 'btn-danger' : 'btn-success';

            $retArr[] = array(
                'type' => 'subTeam',
                'id' => $subTeam->getId(),
                'name' => Html::tag('span', $subTeam->getName()),
                'btn' => Html::submitInput($btnValue, ['class' => 'btn ' . $btnColor, 'name' => 'submitText']),
            );

        }

        return $retArr;

    }

    /**
     * @param $subTeams
     * @param $user
     * @return array
     */
    public function getCheckInBtns($subTeams, $user)
    {
        if ($this->getMode()->one()->getMainPlayer() == 1) {

            $isParticipating = $this->checkPlayerCheckedIn($user);

            $btnValue = ($isParticipating) ? 'Check-Out' : 'Check-In';
            $btnColor = ($isParticipating) ? 'btn-danger' : 'btn-success';

            $tmp = array(
                array(
                    'type' => 'user',
                    'id' => $user->getId(),
                    'name' => Html::tag('span', $user->getUsername()),
                    'btn' => Html::submitInput($btnValue, ['class' => 'btn ' . $btnColor, 'name' => 'submitText']),
                ),
            );
            return $tmp;
        }

        $retArr = array();
        foreach ($subTeams as $key => $subTeam) {

            if ($subTeam->getTournamentModeId() !== $this->getModeId()) {
                continue;
            }

            $modeMainPlayers = $this->getMode()->one()->getMainPlayer();

            $mainFound = 0;
            $teamMembers = SubTeamMember::getTeamMembers($subTeam->getId());
            foreach ($teamMembers as $teamMemberKey => $teamMember) {
                if ($teamMember->getIsSubstitute() === 0) {
                    $mainFound++;
                }
            }

            if ($mainFound < $modeMainPlayers) {
                continue;
            }

            $isParticipating = $this->checkTeamParticipating($subTeam);
            if (!$isParticipating) {
                continue;
            }

            $isParticipating = $this->checkTeamCheckedIn($subTeam);

            $btnValue = ($isParticipating) ? 'Check-Out' : 'Check-In';
            $btnColor = ($isParticipating) ? 'btn-danger' : 'btn-success';

            $retArr[] = array(
                'type' => 'subTeam',
                'id' => $subTeam->getId(),
                'name' => Html::tag('span', $subTeam->getName()),
                'btn' => Html::submitInput($btnValue, ['class' => 'btn ' . $btnColor, 'name' => 'submitText']),
            );

        }

        return $retArr;

    }

    /**
     * @param $user
     * @return boolean
     */
    private function checkPlayerParticipating($user)
    {
        return PlayerParticipating::findPlayerParticipating($this->tournament_id, $user->getId()) != null;
    }

    /**
     * @param $subTeam
     * @return boolean
     */
    private function checkTeamParticipating($subTeam)
    {
        return TeamParticipating::findTeamParticipating($this->tournament_id, $subTeam->getId()) != null;
    }

    /**
     * @param $user User
     * @return boolean
     */
    private function checkPlayerCheckedIn($user)
    {
        return PlayerParticipating::findPlayerCheckedIn($this->tournament_id, $user->getId()) != null;
    }

    /**
     * @param $subTeam SubTeam
     * @return boolean
     */
    private function checkTeamCheckedIn($subTeam)
    {
        return TeamParticipating::findTeamCheckedIn($this->tournament_id, $subTeam->getId()) != null;
    }

    /**
     * @return ActiveQuery
     */
    public function getCup()
    {
        return $this->hasOne(Cups::className(), ['cup_id' => 'cup_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBaseRuleSet()
    {
        return $this->hasOne(TournamentRules::className(), ['rules_id' => 'rules_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSubRuleSet()
    {
        return $this->hasMany(TournamentSubrules::className(), ['rules_id' => 'rules_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(TournamentMode::className(), ['mode_id' => 'mode_id']);
    }

    /**
     * @return Tournament[]
     */
    public static function getRLTournaments()
    {
        //TODO: Die 1 als RL Id solltet ihr in die Constants auslagern. Im Idealfall solltet ihr sogar ne Spiele Tabelle in der DB haben.
        return static::findAll(['game_id' => '1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getParticipants()
    {
        if ($this->getMode()->one()->getMainPlayer() == 1) {
            return $this->hasMany(User::className(), ['user_id' => 'user_id'])->viaTable('player_participating', ['tournament_id' => 'tournament_id']);
        }

        return $this->hasMany(SubTeam::className(), ['sub_team_id' => 'sub_team_id'])->viaTable('team_participating', ['tournament_id' => 'tournament_id']);
    }
}