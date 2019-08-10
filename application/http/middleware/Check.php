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
        if (Cache::get('key')) {
            if ($request->param('key') === Cache::get('key')) {
                return $next($request);
            }
            return json([
                'data' => [],
                'code' => -1,
                'msg' => '授权失败'
            ]);
        }
        if ($request->param('secretKey') === config('api.secretKey')) {
            $key = md5(sha1(uniqid('yse', false) . time()));
            Cache::set('key', $key);
            return json([
                'data' => [
                    'key' => $key
                ],
                'code' => 0,
                'msg' => '成功'
            ]);
        }

        return json([
            'data' => [],
            'code' => -1,
            'msg' => '请求失败'
        ]);
    }
}
