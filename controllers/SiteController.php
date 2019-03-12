<?php

namespace app\controllers;

use app\models\LoginForm;
use app\modules\core\models\Gender;
use app\modules\core\models\Language;
use app\modules\core\models\Nationality;
use app\modules\core\models\User;
use app\modules\core\models\UserForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->render('index');
            //return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /* All Actions for the Logged In User */

    /**
     * Display User Account Informations
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionMyAccount()
    {
        if (Yii::$app->user->isGuest) {
            // return $this->render('index');
            return $this->goHome();
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;

        $gender = $user->getGender()->one();
        $language = $user->getLanguage()->one();
        $nationality = $user->getNationality()->one();

        $allMainTeams = $user->getMainTeams()->all();
        $allMemberTeams = $user->getMemberTeams()->all();

        $mainTeams = [];
        foreach ($allMainTeams as $mainTeam) {
            $mainTeams[] = [
                'owner' => true,
                'team' => $mainTeam
            ];
        }

        foreach ($allMemberTeams as $memberTeam) {
            $mainTeams[] = [
                'owner' => false,
                'team' => $memberTeam
            ];
        }

        $allSubTeams = $user->getSubTeams()->all();
        $allMemberTeams = $user->getSubMemberTeams()->all();

        $subTeams = [];
        foreach ($allSubTeams as $subTeam) {
            $subTeams[] = [
                'owner' => true,
                'team' => $subTeam,
            ];
        }

        foreach ($allMemberTeams as $subMemberTeam) {
            $subTeams[] = array(
                'owner' => false,
                'team' => $subMemberTeam,
            );
        }

        return $this->render('myAccount',
            [
                'model' => $user,
                'gender' => $gender,
                'language' => $language,
                'nationality' => $nationality,
                'mainTeams' => $mainTeams,
                'subTeams' => $subTeams
            ]);
    }

    /**
     * Displays User Tournaments.
     *
     * @return string
     */
    public function actionMyTournaments()
    {
        return $this->render('myTournaments');
    }

    /**
     * Displays User Teams.
     *
     * @return string
     */
    public function actionMyTeams()
    {
        return $this->render('myTeams');
    }

    /**
     * Displays News.
     *
     * @return string
     */
    public function actionNews()
    {
        return $this->render('news');
    }

    /**
     * Displays Twitch.
     *
     * @return string
     */
    public function actionTwitch()
    {
        return $this->render('twitch');
    }

    /**
     * Displays Bracket.
     *
     * @return string
     */
    public function actionBracket()
    {
        return $this->render('bracket');
    }

    /**
     * Register new User action
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionRegister()
    {
        $model = new UserForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            $this->goHome();
        }

        $genderList = [];
        foreach (Gender::find()->all() as $gender) {
            $genderList[$gender->getGenderId()] = $gender->getName();
        }
        $languageList = [];
        foreach (Language::find()->all() as $language) {
            $languageList[$language->getLanguageId()] = $language->getName();
        }

        $nationalityList = [];
        foreach (Nationality::find()->all() as $nationality) {
            $nationalityList[$nationality->getId()] = $nationality->getName();
        }

        return $this->render('register',
            [
                "model" => $model,
                'genderList' => $genderList,
                'languageList' => $languageList,
                'nationalityList' => $nationalityList
            ]);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     *
     * public function actionContact()
     * {
     * $model = new ContactForm();
     * if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
     * Yii::$app->session->setFlash('contactFormSubmitted');
     *
     * return $this->refresh();
     * }
     * return $this->render('contact', [
     * 'model' => $model,
     * ]);
     * }*/

    /**
     * Displays about page.
     *
     * @return string
     *
     * public function actionAbout()
     * {
     * return $this->render('about');
     * }*/
}
