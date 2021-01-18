<?php
namespace App\Models\Traits;

use Cache;

trait CacheList
{
    /**
     * @comment 覆盖衍生表的 all 方法
     * @param array|mixed|string[] $columns
     * @return array |\Illuminate\Database\Eloquent\Collection|void
     * @author Curator
     * @date 2020/5/12
     */
    static public function all($columns = ['*'])
    {
        return Cache::rememberForever(self::class, function() use ($columns) {
            return self::where('status', 1)->select($columns)->get();
        });
    }

    /**
     * @comment 遗忘缓存
     * @author Curator
     * @date 2020/5/12
     */
    public function forgetCacheList()
    {
        Cache::forget(self::class);
    }
}
