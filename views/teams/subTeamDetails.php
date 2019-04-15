<?php
/**
 * Created by PhpStorm.
 * User: Pierre Köhler
 * Date: 18.03.2019
 * Time: 09:15
 */

/* @var $this yii\web\View *
 * @var $profilePicModel ProfilePicForm
 * @var $teamDetails array
 * @var $teamInfo array
 */

use app\modules\core\models\ProfilePicForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* Browser Title */
$this->title = $teamDetails->getName() . '\'s Sub Team profile';

/* Site Canonicals */
$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://project-esport.gg' . Yii::$app->request->url]);

/* twitter/facebook/google Metatags */
Yii::$app->metaClass->writeMetaMainTeam($this, $teamDetails, $this->title);

?>

<div class="site-sub-team-details">
    <div class="col-lg-3 avatarPanel">
        <img class="avatar-logo" src="<?= $teamInfo['teamImage']; ?>.webp" alt=""
             onerror="this.src='<?= $teamInfo['teamImage']; ?>.png'">

        <?php if ($teamInfo['isOwner']) : ?>
            <?php $form = ActiveForm::begin([
                'id' => 'profile-pic-form',
                // 'layout' => 'horizontal',
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'template' => "<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>"
                ],
            ]); ?>
            <?= $form->field($profilePicModel, 'id')->hiddenInput()->label(false); ?>
            <?= $form->field($profilePicModel, 'file')->fileInput() ?>
            <?= Html::submitButton(Yii::t('app', 'upload')); ?>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-7 teamPanel">

        <div class="header">
            <span class="teamname"><?= $teamDetails->getName(); ?></span>
            <span class="teamid">id: <?= $teamDetails->getId(); ?></span>
        </div>
        <div class="entry clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Name</div>
            <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $teamDetails->getName(); ?></div>
        </div>
        <div class="entry clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Shortcode</div>
            <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $teamDetails->getShortCode(); ?></div>
        </div>
        <div class="entry clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Team Captain</div>
            <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= Html::a($teamDetails->GetTeamCaptain()->one()->getUsername(), ['/user/details', 'id' => $teamDetails->getTeamCaptainId()]); ?></div>
        </div>
        <div class="entry clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Mitglied Seit</div>
            <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $teamInfo['memberSince']; ?></div>
        </div>
        <div class="entry clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Nationalität</div>
            <div class="col-xs-7 col-sm-9 col-lg-9 context"></div>
        </div>
        <div class="entry clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Description</div>
            <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $teamDetails->getDescription() ?></div>
        </div>
        <div class="entry entryMembers clearfix">
            <div class="col-xs-5 col-sm-3 col-lg-3">Team-Members</div>
            <div class="col-xs-7 col-sm9 col-lg-9 context">
                <?php foreach($teamDetails->getSubTeamMembers()->all() as $userKey => $user): ?>
                    <?php
                        $username = $user->getUser()->one()->getUsername();
                        $userId = $user->getUserId();
                    ?>
                    <div class="col-lg-6 teamMembers"><?= Html::a($username, ['/user/details', 'id' => $userId]); ?>
                        (<?= ($teamDetails->isUserSubstitute($userId)) ? 'substitute' : 'player'; ?>)
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <!-- falls hier noch was reinkommen sollte. -->
    </div>
</div>
