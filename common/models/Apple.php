<?php

namespace common\models;

use PHPUnit\Util\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property string $color
 * @property int $created_at
 * @property int|null $fell_at
 * @property int $status
 * @property int $eaten
 */
class Apple extends ActiveRecord
{
    const STATUS_ON_TREE = 0;
    const STATUS_FALLEN = 1;
    const STATUS_ROTTEN = 2;

/*    public $color;
    public $created_at;
    public $fell_at;
    public $eaten;
    public $status;*/

    public static function tableName()
    {
        return 'apple';
    }

    public function rules()
    {
        return [
            [['color', 'created_at'], 'required'],
            [['created_at', 'fell_at', 'eaten', 'status'], 'integer'],
            [['color'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => [self::STATUS_ON_TREE, self::STATUS_FALLEN, self::STATUS_ROTTEN]],
            [['eaten'], 'integer', 'min' => 0, 'max' => 100],
        ];
    }

    public function init($color = '')
    {
        parent::init();
        if ($this->isNewRecord) { // Только для новых записей

            parent::init();

            if ($color == '') {
                $this->color = $this->generateRandomColor();
            } else {
                $this->color = $color;
            }
            $this->created_at = $this->generateRandomCreationTime();
            $this->status = self::STATUS_ON_TREE;
            $this->eaten = 0;
        }
    }

    function generateRandomColor()
    {
        $color = ['red', 'green', 'yellow'];
        return $color[array_rand($color)];
    }

    private function generateRandomCreationTime()
    {
        $oneMonthAgo = strtotime('-1 month');
        return rand($oneMonthAgo, time());
    }


    public function getSize()
    {
        return(100 - $this->eaten) / 100.0;
    }


    public function getStatusText()
    {
        if ($this->isRotten()) {
            return 'Гнилое';
        }

        switch ($this->status) {
            case self::STATUS_ON_TREE:
                return 'На дереве';
            case self::STATUS_FALLEN:
                return 'Упало';
            default:
                return 'Неизвестно';
        }
    }

    public function canFall()
    {
        return $this->status === self::STATUS_ON_TREE;
    }

    public function fallToGround()
    {
        if (!$this->canFall()) {
            throw new Exception("Яблоко уже упало");
        }
        $this->status = self::STATUS_FALLEN;
        $this->fell_at = time();
        $this->save();
    }

    public function isRotten()
    {
        if ($this->status !== self::STATUS_FALLEN) {
            return false;
        }

        return time() - $this->fell_at >= 5*3600;
    }

    public function updateRottenState()
    {
        if ($this->status == self::STATUS_FALLEN && $this->isRotten()) {
            $this->status = self::STATUS_ROTTEN;
            $this->save();
        }
    }

    public function eat($percent)
    {
        $this->updateRottenState();

        if ($this->status === self::STATUS_ON_TREE) {
            throw new Exception("Съесть нелься - яблоко на дереве");
        }

        if ($this->status === self::STATUS_ROTTEN) {
            throw new Exception("Съесть нелься - яблоко сгнило");
        }

        if ($percent <= 0 || $percent > 100) {
            throw new \Exception("Неверное значение процента. Процент должен быть от 1 до 100");
        }

        $this->eaten = $this->eaten+$percent;

        if ($this->eaten >= 100) {
            return $this->delete();
        }

        if (!$this->save()) {
            throw new Exception('Не удалось сохранить');
        }
        return true;
    }

}