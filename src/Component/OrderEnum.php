<?php

namespace ZhijiaCommon\Component;

use MyCLabs\Enum\Enum;

class OrderEnum extends Enum {
  // 待付款
  public const ORDER_UNPAID = 0;
  // 待处理
  public const ORDER_PENDING = 1;
  // 待发货
  public const ORDER_UNSENT = 11;
  // 待收货
  public const ORDER_RECEIVING = 2;
  // 已完成
  public const ORDER_COMPLETED = 3;
  // 已取消
  public const ORDER_CANCEL = 4;
  // 已拆单
  public const ORDER_ORIGINAL = 5;
  // 待评价
  public const ORDER_UNCOMMENT = 6;
  // 售后通过
  public const ORDER_AFTER = 7;

}
