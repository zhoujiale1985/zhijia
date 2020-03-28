<?php

namespace ZhijiaCommon\Utils;

/**
 * 通用工具类
 */
class CommonUtils
{
    public function getRandomNum($length)
    {
        $nums = '0123456789';
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $nums[mt_rand(0, strlen($nums) - 1)];
        }
        return $rand;
    }

    public function getRandomString($len)
    {
        $chars = "ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678";
        $pwd = '';
        for ($i = 0; $i < $len; $i++) {
            $pwd .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $pwd;
    }

    public function transformStringExcludeSymbols($str)
    {
        $comma = array('`','×','=','|','-',':','·','‘','’','“','”','，','。','《','》','？','；','【','】','、','～','『','』','（','）','…','￥','！','—', '○', '︴','↕','⌒', '▕','∫','┊','‖', '﹗','｜','│','┇', '┋','┆','┃');
        $new_str = "";
        for ($i = 0; $i < strlen($str); $i++)
        {
            if (($str[$i] >= 'A' && $str[$i] <= 'Z')
                || ($str[$i] >= 'a' && $str[$i] <= 'z')
                || ($str[$i] >= '0' && $str[$i] <= '9')
                || ord($str[$i]) > 127)
            {
                $new_str .= strtolower($str[$i]);
            }
        }
        foreach ( $comma as $c)
        {
            $new_str = str_replace($c, "", $new_str);
        }

        return $new_str;
    }

    // uuid
    public function gen_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    public function gen_simple_uuid(){
        return sprintf('%04x%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    public function getExceptionTraceAsString($exception) {
        $rtn = "";
        $count = 0;
        foreach ($exception->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        //如果长度大于256，只获取前256字符和...提示
                        if (strlen($arg) >256) {
                            $args[] = "'" . substr($arg, 0, 256) . "...'";
                        } else {
                            $args[] = "'" . $arg . "'";
                        }
                    } elseif (is_array($arg)) {
                        $args[] = "Array";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            $rtn .= sprintf( "#%s %s(%s): %s(%s),",
                $count,
                isset($frame['file']) ? $frame['file'] : 'unknown file',
                isset($frame['line']) ? $frame['line'] : 'unknown line',
                (isset($frame['class']))  ? $frame['class'].'->'.$frame['function'] : $frame['function'],
                $args );
            $count++;
        }
        return $rtn;
    }

    /**
     * 返回当前毫秒时间戳
     *
     */
    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * 日期时间转成毫秒时间戳
     */
    public static function getDateToMsec($msecdate)
    {
        $date = strtotime($msecdate);
        $return_data = str_pad($date,13,"0",STR_PAD_RIGHT);
        return $msectime = $return_data;
    }

    /**
     * 构造树形结构数据
     * @param string $columnKey
     * @param $list array
     * @return array
     */
    public static function constructTreeStructure($columnKey='id', array $list): array
    {
        //初始化数据 parent id 为 key 的结构
        $structure= [];
        foreach ($list as $item) {
            $structure[$item[$columnKey]] = $item;
        }
        unset($item);
        //初始化树形结构数据
        $treeStructure = [];
        //引用遍历数据
        //由于所有数据的 key 为自身 id ，所以使用 isset 来判断数据中是否存在当前数据的 pid
        //（也可以使用 array_key_exsits() 函数，由于数据表的字段原因，这里使用parent）
        //当判断为 true 时，在当前数据中创建 children 字段数组。
        //采用引用的方式将符合条件的数据押入 children 数组中。
        //由于采用的是引用，所以访问的是同一个变量内容。
        //每次循环都会寻找是否存在 pid ，如果有，将 children 中加入引用。
        //关系：父级->引用子级->引用孙子级...
        //那么在修改子级的时候，由于父级中保存的是子级的引用，所以在子级中添加引用的孙子级时，
        //父级中的内容也会发生变化(即在父级的子级中添加了孙子级)
        //当判断为 false ，则证明当前数据是父级，此时引用存入树形结构数组，
        //保证新数组中只有从父级开始的树形结构。
        //注：此时的任何修改也会影响树形结构数组中的内容，原因是数据之间都是通过引用来实现的。
        foreach ($structure as $key => $item) {
            if (isset($structure[$item['parent']])) {
                $structure[$item['parent']]['children'][] = &$structure[$key];
            } else {
                $treeStructure[] = &$structure[$key];
            }
        }
        return $treeStructure;
    }
}
