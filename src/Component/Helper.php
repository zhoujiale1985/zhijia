<?php
/**
 *
 * User: nintenichi
 * Date: 2019/10/10 5:06 下午
 * Email: <rentianyi@homme-inc.com>
 */
namespace ZhijiaCommon\Component;

/**
 * Class Helper
 * @package ZhijiaCommon\Component
 */
class Helper
{
    /**
     * 对应key的字段名，默认为'ID'
     * @var string
     */
    public static $columnKey = 'id';

    /**
     * 构造树形结构数据
     * @param $list array
     * @return array
     */
    public static function constructTreeStructure(array $list): array
    {
        //初始化数据 parent id 为 key 的结构
        $structure= [];
        foreach ($list as $item) {
            $structure[$item[self::$columnKey]] = $item;
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
