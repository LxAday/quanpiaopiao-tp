<?php

//api接口配置文件
return[
    //应用的key
    'appKey'                 => '5d4c1263cca60',
    //应用的Secret
    'appSecret'              => '13dd69342526f9f895dd93dd0b3eeab2',
    //接口授权秘钥
    'secretKey'              => 'fe8684fa9acc2bbb00f5be238962b390d9b30a18',

    //请求地址列表

    //商品列表
    'get_goods_list'         =>'https://openapi.dataoke.com/api/goods/get-goods-list',
    //我的收藏
    'get_collection_list'    =>'https://openapi.dataoke.com/api/goods/get-collection-list',
    //商品更新
    'get_newest_goods'       =>'https://openapi.dataoke.com/api/goods/get-newest-goods',
    //热搜记录
    'get_top100'             =>'https://openapi.dataoke.com/api/category/get-top100',
    //高效转链
    'get_privilege_link'     =>'https://openapi.dataoke.com/api/tb-service/get-privilege-link',
    //单品详情
    'get_goods_details'      =>'https://openapi.dataoke.com/api/goods/get-goods-details',
    //大淘客搜索
    'get_dtk_search_goods'   =>'https://openapi.dataoke.com/api/goods/get-dtk-search-goods',
    //超级分类
    'get_super_category'     =>'https://openapi.dataoke.com/api/category/get-super-category',
    //失效商品
    'get_stale_goods_by_time'=>'https://openapi.dataoke.com/api/goods/get-stale-goods-by-time',
    //9.9包邮精选
    'op_goods_list'          =>'https://openapi.dataoke.com/api/goods/nine/op-goods-list',

];