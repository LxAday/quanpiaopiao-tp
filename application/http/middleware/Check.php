<?php

namespace app\http\middleware;

use think\facade\Cache;

class Check
{

    /**
     * 接口授权
     * @param $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     */
    public function handle($request, \Closure $next)
    {
        if (Cache::get($request->param('userId'))) {
            if ($request->param('key') === Cache::get($request->param('userId'))) {
                return $next($request);
            }
            return json([
                'data' => [],
                'code' => -1,
                'msg' => '授权失败'
            ]);
        }
        return json([
            'data' => [],
            'code' => -1,
            'msg' => '请求失败'
        ]);
    }
}
