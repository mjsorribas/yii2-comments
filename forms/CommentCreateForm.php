<?php
/**
 * CommentCreateForm.php
 * @author Revin Roman
 * @link https://rmrevin.ru
 */

namespace rmrevin\yii\module\Comments\forms;

use rmrevin\yii\module\Comments;

/**
 * Class CommentCreateForm
 * @package rmrevin\yii\module\Comments\forms
 */
class CommentCreateForm extends \yii\base\Model
{

    public $id;
    public $entity;
    public $text;

    /** @var Comments\models\Comment */
    public $Comment;

    public function init()
    {
        $Comment = $this->Comment;

        if (false === $this->Comment->isNewRecord) {
            $this->id = $Comment->id;
            $this->entity = $Comment->entity;
            $this->text = $Comment->text;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $CommentModelClassName =  \Yii::$app->getModule(Comments\Module::NAME)->model('comment');

        return [
            [['entity', 'text'], 'required'],
            [['entity', 'text'], 'string'],
            [['id'], 'integer'],
            [['id'], 'exist', 'targetClass' => $CommentModelClassName, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'entity' => \Yii::t('app', 'Entity'),
            'text' => \Yii::t('app', 'Text'),
        ];
    }

    /**
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     */
    public function save()
    {
        $Comment = $this->Comment;

        $CommentModelClassName =  \Yii::$app->getModule(Comments\Module::NAME)->model('comment');

        if (empty($this->id)) {
            $Comment =  \Yii::createObject($CommentModelClassName);
        } elseif ($this->id > 0 && $Comment->id !== $this->id) {
            /** @var Comments\models\Comment $CommentModel */
            $CommentModel =  \Yii::createObject($CommentModelClassName);
            $Comment = $CommentModel::find()
                ->byId($this->id)
                ->one();

            if (!($Comment instanceof Comments\models\Comment)) {
                throw new \yii\web\NotFoundHttpException;
            }
        }

        $Comment->entity = $this->entity;
        $Comment->text = $this->text;

        $result = $Comment->save();

        if ($Comment->hasErrors()) {
            foreach ($Comment->getErrors() as $attribute => $messages) {
                foreach ($messages as $mes) {
                    $this->addError($attribute, $mes);
                }
            }
        }

        $this->Comment = $Comment;

        return $result;
    }
}