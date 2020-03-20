<?php
/**
 *
 * User: nintenichi
 * Date: 2019/10/30 11:25 上午
 * Email: <rentianyi@homme-inc.com>
 */

namespace ZhijiaCommon\Component;

/**
 * Class FromConstant
 * @package ZhijiaCommon\Component
 */
class FromConstant
{
    /**
     * 获取表单常量组件
     * Get from constant component
     * @param string $select
     * @return array
     */
    public static function getFromConstant($select = 'all'): array
    {
        $constant = self::constant();
        if ($select !== 'all') {
            if (!array_key_exists($select, $constant)) {
                return [];
            }
            return $constant[$select];
        }
        return $constant;
    }

    /**
     * 表单常量
     * From constant
     * @return array
     */
    private static function constant(): array
    {
        return [
            //网店类型
            'online_shop_type' => [
                1 => '官网',
                2 => '淘宝网',
                3 => '天猫',
            ],
            //主营风格
            'main_style' => [
                1 => '北欧',
                2 => '现代',
                3 => '轻奢',
                4 => '美式',
                5 => '新中式',
                6 => '中式',
                7 => '法式',
                8 => '欧式',
                9 => '新古典',
                10 => '地中海',
                11 => '日式',
            ],
            //主要用料
            'main_material' => [
                1 => '橡木',
                2 => '胡桃木',
                3 => '樱桃木',
                4 => '白蜡木',
                5 => '枫木',
                6 => '柚木',
                7 => '水曲柳',
            ],
            //家具拼接工艺
            'splicing_process' => [
                1 => '榫卯',
                2 => '钉接',
                3 => '胶粘'
            ],
            //环保等级
            'environmental_rating' => [
                0 => '请选择环保等级',
                1 => 'E0级',
                2 => 'F4星级',
                3 => 'E1级',
                4 => 'E2级',
            ],
            //结算方式
            'settlement_method' => [
                0 => '请选择结算方式',
                1 => '日结',
                2 => '月结'
            ],
            //下单方式
            'order_method' => [
                0 => '请选择下单方式',
                1 => '线上下单',
                2 => '线下下单'
            ],
            //发票类型
            'invoice_type' => [
                1 => '普票',
                2 => '专票',
            ],
            //合作物流
            'logistics' => [
                1 => '顺丰快递',
                2 => '圆通快递',
                3 => '韵达快递',
                4 => '申通快递',
                5 => '中通快递',
                6 => 'EMS',
                7 => '天天快递',
                8 => '百世快递',
                9 => '全峰快递',
                10 => '邮政快递',
                11 => '宅急送',
                12 => '快捷速递',
                13 => '德邦物流',
                14 => '天地华宇',
                15 => '龙邦速递',
                16 => '优速物流',
                17 => '中邮物流',
                19 => '传喜物流',
                20 => '中铁物流',
                21 => '飞康达',
                22 => '中铁物流',
            ],
            //货物信息
            'goods_info' => [
                1 => '现货',
                2 => '定制',
                3 => '现货/定制',
            ]
        ];
    }
}
