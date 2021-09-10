<?php
namespace app\grids;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\helpers\Html;

class AbstractGrid extends Model
{
    public DataProviderInterface $provider;

    /**
     * @return DataProviderInterface
     */
    public function getProvider(): DataProviderInterface
    {
        return $this->provider;
    }

    /**
     * @param DataProviderInterface $provider
     */
    public function setProvider(DataProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @param float $value
     * @param int $decimals
     * @return string
     */
    protected function asFormatNumber(float $value, int $decimals = 2): string
    {
        return \Yii::$app->getFormatter()->asDecimal($value, $decimals);
    }

    /**
     * @param string $value
     * @param array $options
     * @return string
     */
    protected function renderHtmlTextArea(string $value = '', array $options = []): string
    {
        return Html::textarea('', $value, array_merge([
            'class' => 'form-control input-sm',
            'onclick' => 'this.focus();this.select()',
            'readonly' => true
        ], $options));
    }

    /**
     * @param $value
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function asFormatMoney($value): string
    {
        return \Yii::$app->getFormatter()->asCurrency($value);
    }

    /**
     * @param $value
     * @param null $deciaml
     * @return string
     */
    protected function asDecimal($value, $deciaml = null): string
    {
        return \Yii::$app->getFormatter()->asDecimal($value, $deciaml);
    }

}
