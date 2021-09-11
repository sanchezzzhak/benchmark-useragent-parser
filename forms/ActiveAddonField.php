<?php


namespace app\forms;


use yii\widgets\ActiveField;

class ActiveAddonField extends ActiveField
{
    public $template = '{label}<div class="input-group">{addon} {input} {hint} {error}</div>';

    public $addonContent = '';

    public function init()
    {
        parent::init();
        $this->parts['{addon}'] = '';
    }


    public function addon($content = null)
    {
        $this->parts['{addon}'] = sprintf(
            '<div class="input-group-prepend"><div class="input-group-text">%s</div></div>',
            $content);

        return $this;
    }
}