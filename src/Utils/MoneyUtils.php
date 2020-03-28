<?php


namespace ZhijiaCommon\Utils;


class MoneyUtils
{
    const  MONEY = 100;

    //分 -> 元
    public function pointransform($orders, $is_list = false, $is_after_info = false)
    {

        if ($is_list) {
            foreach ($orders as $key => $value) {
                $orders[$key] = $this->transformOrder($value);
            }
            return $orders;
        } else {
            $orders = $this->transformOrder($orders);
            if ($is_after_info) {
                if ((array_key_exists('refund_info', $orders) && array_key_exists('refund_id', $orders['refund_info'])) || (array_key_exists('return_skus', $orders) && array_key_exists('return_id', $orders['return_skus']))) {
                    $orders = $this->transformAfter($orders);
                } else {
                    if (array_key_exists('refund_info', $orders)) {
                        foreach ($orders['refund_info'] as $key => $order) {
                            $orders['refund_info'][$key] = $this->transformAfter($order);
                        }
                    } elseif (array_key_exists('return_skus', $orders)) {
                        foreach ($orders['return_skus'] as $key => $order) {
                            $orders['return_skus'][$key] = $this->transformAfter($order);
                        }
                    }
                }
            }
            return $orders;
        }
    }

    public static function transformSku($sku)
    {
        $sku['sku_price'] = key_exists('sku_price', $sku) ? $sku['sku_price'] / self::MONEY : 0;
        $sku['cost_price'] = key_exists('cost_price', $sku) ? $sku['cost_price'] / self::MONEY : 0;
        $sku['market_price'] = key_exists('market_price', $sku) ? $sku['market_price'] / SELF::MONEY : 0;
        return $sku;
    }

    private function transformOrder($item)
    {
        array_key_exists('all_price', $item) ? $item['all_price'] = $item['all_price'] / self::MONEY : null;
        array_key_exists('all_discount_price', $item) ? $item['all_discount_price'] = $item['all_discount_price'] / self::MONEY : null;
        array_key_exists('actual_price', $item) ? $item['actual_price'] = $item['actual_price'] / self::MONEY : null;
        array_key_exists('coupon_price', $item) ? $item['coupon_price'] = $item['coupon_price'] / self::MONEY : null;
        array_key_exists('increase_price', $item) ? $item['increase_price'] = $item['increase_price'] / self::MONEY : null;
        array_key_exists('reduce_price', $item) ? $item['reduce_price'] = $item['reduce_price'] / self::MONEY : null;

        if ($item['discounts'] != null && !empty($item['discounts'])) {
            if (array_key_exists('discount_code', $item['discounts'])) {
                array_key_exists('price', $item['discounts']['discount_code']) ? $item['discounts']['discount_code']['price'] = $item['discounts']['discount_code']['price'] / self::MONEY : null;
                array_key_exists('self_discount_price', $item['discounts']['discount_code']) ? $item['discounts']['discount_code']['self_discount_price'] = $item['discounts']['discount_code']['self_discount_price'] / self::MONEY : null;
            }
            if (array_key_exists('discount_coupons', $item['discounts'])) {
                $discount_coupons = $item['discounts']['discount_coupons'];
                foreach ($discount_coupons as $key => $coupon) {
                    array_key_exists('price', $coupon) ? $discount_coupons[$key]['price'] = $coupon['price'] / self::MONEY : null;
                    array_key_exists('self_discount_price', $coupon) ? $discount_coupons[$key]['self_discount_price'] = $coupon['self_discount_price'] / self::MONEY : null;
                }
                $item['discounts']['discount_coupons'] = $discount_coupons;
            }
        }
        $skus = $item['skus'];
        foreach ($skus as $key => $value) {
            array_key_exists('sku_price', $value) ? $skus[$key]['sku_price'] = $value['sku_price'] / self::MONEY : null;
            array_key_exists('price_cost', $value) ? $skus[$key]['price_cost'] = $value['price_cost'] / self::MONEY : null;
            array_key_exists('price_original', $value) ? $skus[$key]['price_original'] = $value['price_original'] / self::MONEY : null;
        }
        $item['skus'] = $skus;
        return $item;
    }

    private function transformAfter($item)
    {
        // 退货 分 -> 元
        if (array_key_exists('return_skus', $item) && !empty($item['return_skus'])) {
            $sku = $item['return_skus'];
            array_key_exists('refund_price', $sku) ? $sku['refund_price'] = $sku['refund_price'] / self::MONEY : null;
            array_key_exists('handling_fee', $sku) ? $sku['handling_fee'] = $sku['handling_fee'] / self::MONEY : null;
            $item['return_skus'] = $sku;
        }
        // 退款 分 -> 元
        if (array_key_exists('refund_info', $item) && !empty($item['refund_info'])) {
            $info = $item['refund_info'];
            array_key_exists('refund_price', $info) ? $info['refund_price'] = $info['refund_price'] / self::MONEY : null;
            array_key_exists('handling_fee', $info) ? $info['handling_fee'] = $info['handling_fee'] / self::MONEY : null;
            $item['refund_info'] = $info;
        }
        return $item;
    }
}
