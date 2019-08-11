<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//接口授权
Route::get('accredit', 'index/accredit');

//接口路由组
Route::group('api', static function () {
//商品列表
    Route::get('get-goods-list', 'index/get_goods_list');
//我的收藏
    Route::get('get-collection-list', 'index/get_collection_list');
//商品更新
    Route::get('get-newest-goods', 'index/get_newest_goods');
//热搜记录
    Route::get('get-top100', 'index/get_top100');
//高效转链
    Route::get('get-privilege-link', 'index/get_privilege_link');
//单品详情
    Route::get('get-goods-details', 'index/get_goods_details');
//大淘客搜索
    Route::get('get-dtk-search-goods', 'index/get_dtk_search_goods');
//超级分类
    Route::get('get-super-category', 'index/get_super_category');
//失效商品
    Route::get('get-stale-goods-by-time', 'index/get_stale_goods_by_time');
//9.9包邮精选
    Route::get('op-goods-list', 'index/op_goods_list');
})->middleware('Check');
echo  4444;