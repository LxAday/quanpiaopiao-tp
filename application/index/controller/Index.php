<?php

namespace app\index\controller;

use GuzzleHttp\Client;
use think\facade\Cache;

class Index
{
    /**
     * 接口授权
     * @return object
     */
    public function accredit(): object
    {
        if (request()->get('secretKey') === config('api.secretKey')) {
            $key = md5(sha1(uniqid('yes', false) . time()));
            Cache::set(request()->get('userId'), $key, 600);
            return json([
                'data' => [
                    'key' => $key
                ],
                'code' => 0,
                'msg' => '授权成功'
            ]);
        }
        return json([
            'data' => [],
            'code' => -1,
            'msg' => '授权失败'
        ]);
    }


    /**
     * 对接接口验证方法
     * @param string $host
     * @param array $data
     * @return object
     */
    protected static function signature(string $host, array $data): object
    {


        /**
         * 参数加密
         * @param array $data
         * @param string $appSecret
         * @return string
         */
        function makeSign(array $data, string $appSecret): string
        {
            ksort($data);
            $str = '';
            foreach ($data as $k => $v) {

                $str .= '&' . $k . '=' . $v;
            }
            $str = trim($str, '&');

            $sign = strtoupper(md5($str . '&key=' . $appSecret));

            return $sign;
        }

        $appKey = config('api.appKey');//应用的key

        $appSecret = config('api.appSecret');//应用的Secret

        $data['appKey'] = $appKey;

        //加密的参数
        $data['sign'] = makeSign($data, $appSecret);

        //拼接请求地址
        $url = $host . '?' . http_build_query($data);

        //执行请求获取数据
        $http = new Client([
            'verify' => false
        ]);

        $resource = $http->get($url)->getBody();

        return $resource;
    }

    /**
     * 商品列表
     * @return object
     */
    public function get_goods_list(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.1
            'version' => 'v1.0.1',
            //默认为100，可选范围：10,50,100,200，如果小于10按10处理，大于200按照200处理，其它非范围内数字按100处理
            'pageSize' => 100,
            //默认为1，支持传统的页码分页方式和scroll_id分页方式，根据用户自身需求传入值。示例1：商品入库，则首次传入1，后续传入接口返回的pageid，接口将持续返回符合条件的完整商品列表，该方式可以避免入口商品重复；示例2：根据pagesize和totalNum计算出总页数，按照需求返回指定页的商品（该方式可能在临近页取到重复商品）
            'pageId' => '1',
            //默认为0，0-综合排序，1-商品上架时间从高到低，2-销量从高到低，3-领券量从高到低，4-佣金比例从高到低，5-价格（券后价）从高到低，6-价格（券后价）从低到高
            'sort' => '0',
            //大淘客的一级分类id，如果需要传多个，以英文逗号相隔，如：”1,2,3”。当一级类目id和二级类目id同时传入时，会自动忽略二级类目id
            'cids' => '',
            //大淘客的二级类目id，通过超级分类API获取。仅允许传一个二级id，当一级类目id和二级类目id同时传入时，会自动忽略二级类目id
            'scid' => 0,
            //1-聚划算商品，0-所有商品，不填默认为0
            'juHuaSuan' => 0,
            //1-淘抢购商品，0-所有商品，不填默认为0
            'taoQiangGou' => 0,
            //1-天猫商品，0-所有商品，不填默认为0
            'tmall' => 0,
            //1-天猫超市商品，0-所有商品，不填默认为0
            'tchaoshi' => 0,
            //1-金牌卖家，0-所有商品，不填默认为0
            'goldSeller' => 0,
            //1-海淘商品，0-所有商品，不填默认为0
            'haitao' => '',
            //1-预告商品，0-非预告商品
            'pre' => 0,
            //1-品牌商品，0-所有商品，不填默认为0
            'brand' => 0,
            //当brand传入0时，再传入brandIds将获取不到结果。品牌id可以传多个，以英文逗号隔开，如：”345,321,323”
            'brandIds' => '',
            //价格（券后价）下限
            'priceLowerLimit' => '',
            //价格（券后价）上限
            'priceUpperLimit' => '',
            //最低优惠券面额
            'couponPriceLowerLimit' => '',
            //最低佣金比率
            'commissionRateLowerLimit' => '',
            //最低月销量
            'monthSalesLowerLimit' => ''
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_goods_list'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 我的收藏
     * @return object
     */
    public function get_collection_list(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.1
            'version' => 'v1.0.1',
            //默认为100，可选范围：10,50,100,200，如果小于10按10处理，大于200按照200处理，其它非范围内数字按100处理
            'pageSize' => 100,
            //默认为1，支持传统的页码分页方式和scroll_id分页方式，根据用户自身需求传入值。示例1：商品入库，则首次传入1，后续传入接口返回的pageid，接口将持续返回符合条件的完整商品列表，该方式可以避免入口商品重复；示例2：根据pagesize和totalNum计算出总页数，按照需求返回指定页的商品（该方式可能在临近页取到重复商品）
            'pageId' => '1',
            //大淘客的一级分类id，如果需要传多个，以英文逗号相隔，如：”1,2,3”。当一级类目id和二级类目id同时传入时，会自动忽略二级类目id，1 -女装，2 -母婴，3 -美妆，4 -居家日用，5 -鞋品，6 -美食，7 -文娱车品，8 -数码家电，9 -男装，10 -内衣，11 -箱包，12 -配饰，13 -户外运动，14 -家装家纺
            'cid' => '',
            //（如果为是1，则返回全部商品（包含在线商品），为0只返回在线商品）默认返回全部商品
            'trailerType' => 1,
            //默认为0，0-综合排序，1-商品上架时间从高到低，2-销量从高到低，3-领券量从高到低，4-佣金比例从高到低，5-价格（券后价）从高到低，6-价格（券后价）从低到高
            'sort' => 0,
            //加入收藏时间
            'collectionTimeOrder' => ''
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_collection_list'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);

    }


    /**
     * 商品更新
     * @return object
     */
    public function get_newest_goods(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.1
            'version' => 'v1.0.1',
            //默认为100，可选范围：10,50,100,200，如果小于10按10处理，大于200按照200处理，其它非范围内数字按100处理
            'pageSize' => 100,
            //默认为1，支持传统的页码分页方式和scroll_id分页方式，根据用户自身需求传入值。示例1：商品入库，则首次传入1，后续传入接口返回的pageid，接口将持续返回符合条件的完整商品列表，该方式可以避免入口商品重复；示例2：根据pagesize和totalNum计算出总页数，按照需求返回指定页的商品（该方式可能在临近页取到重复商品）
            'pageId' => '1',
            //商品上架开始时间 请求格式：yyyy-MM-dd HH:mm:ss
            'startTime' => '',
            //商品上架结束时间 请求格式：yyyy-MM-dd HH:mm:ss
            'endTime' => '',
            //大淘客的一级分类id，如果需要传多个，以英文逗号相隔，如：”1,2,3”。当一级类目id和二级类目id同时传入时，会自动忽略二级类目id，1 -女装，2 -母婴，3 -美妆，4 -居家日用，5 -鞋品，6 -美食，7 -文娱车品，8 -数码家电，9 -男装，10 -内衣，11 -箱包，12 -配饰，13 -户外运动，14 -家装家纺
            'cids' => '',
            //大淘客的二级类目id，通过超级分类API获取。仅允许传一个二级id，当一级类目id和二级类目id同时传入时，会自动忽略二级类目id
            'scids' => '',
            //1-聚划算商品，0-所有商品，不填默认为0
            'juHuaSuan' => 0,
            //1-淘抢购商品，0-所有商品，不填默认为0
            'taoQiangGou' => 0,
            //1-天猫商品，0-所有商品，不填默认为0
            'tmall' => 0,
            //1-天猫超市商品，0-所有商品，不填默认为0
            'tchaoshi' => 0,
            //1-金牌卖家，0-所有商品，不填默认为0
            'goldSeller' => 0,
            //1-海淘商品，0-所有商品，不填默认为0
            'haitao' => '',
            //1-品牌商品，0-所有商品，不填默认为0
            'brand' => 0,
            //当brand传入0时，再传入brandIds将获取不到结果。品牌id可以传多个，以英文逗号隔开，如：”345,321,323”
            'brandIds' => '',
            //价格（券后价）下限
            'priceLowerLimit' => '',
            //价格（券后价）上限
            'priceUpperLimit' => '',
            //最低优惠券面额
            'couponPriceLowerLimit' => '',
            //最低佣金比率
            'commissionRateLowerLimit' => '',
            //最低月销量
            'monthSalesLowerLimit' => '',
            //默认为0，0-综合排序，1-商品上架时间从新到旧，2-销量从高到低，3-领券量从高到低，4-佣金比例从高到低，5-价格（券后价）从高到低，6-价格（券后价）从低到高
            'sort' => '0'
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_newest_goods'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 热搜记录
     * @return object
     */
    public function get_top100(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.1
            'version' => 'v1.0.1'
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_top100'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        $data['data']['hotWords'] = array_slice($data['data']['hotWords'], 0, 10);
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 高效转链
     * @return object
     */
//    public function get_privilege_link(): object
//    {
//        //定义默认请求参数
//        $arr = [
//            //当前版本： v1.0.5
//            'version' => 'v1.0.5',
//            //淘宝商品id
//            'goodsId' => '',
//            //商品的优惠券ID，一个商品在联盟可能有多个优惠券，可通过填写该参数的方式选择使用的优惠券，请确认优惠券ID正确，否则无法正常跳转
//            'couponId' => '',
//            //用户可自由填写当前大淘客账号下已授权淘宝账号的任一pid，若未填写，则默认使用创建应用时绑定的pid
//            'pid' => ''
//        ];
//        //接收请求并合并参数
//        $arr = array_merge($arr, input('get.'));
//        $data = static::signature(config('api.get_privilege_link'), $arr);
//        $data = json_decode($data, true);
//        //失败
//        if ($data['code'] !== 0) {
//            return json([
//                'data' => [],
//                'code' => -1,
//                'msg' => $data['msg']
//            ]);
//        }
//        //成功
//        return json([
//            'data' => $data['data'],
//            'code' => 0,
//            'msg' => $data['msg']
//        ]);
//    }

    /**
     * 单品详情
     * @return object
     */
    public function get_goods_details(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.2
            'version' => 'v1.0.2',
            //大淘客商品id，请求时id或goodsId必填其中一个，若均填写，将优先查找当前单品id
            'id' => '',
            //id或goodsId必填其中一个，若均填写，将优先查找当前单品id
            'goodsId' => ''
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_goods_details'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 大淘客搜索
     * @return object
     */
    public function get_dtk_search_goods(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v2.0.0
            'version' => 'v2.0.0',
            //默认为100，可选范围：10,50,100,200，如果小于10按10处理，大于200按照200处理，其它非范围内数字按100处理
            'pageSize' => 100,
            //默认为1，支持传统的页码分页方式和scroll_id分页方式，根据用户自身需求传入值。示例1：商品入库，则首次传入1，后续传入接口返回的pageid，接口将持续返回符合条件的完整商品列表，该方式可以避免入口商品重复；示例2：根据pagesize和totalNum计算出总页数，按照需求返回指定页的商品（该方式可能在临近页取到重复商品）
            'pageId' => '1',
            //关键词搜索
            'keyWords' => '',
            //大淘客的一级分类id，如果需要传多个，以英文逗号相隔，如：”1,2,3”。当一级类目id和二级类目id同时传入时，会自动忽略二级类目id，1 -女装，2 -母婴，3 -美妆，4 -居家日用，5 -鞋品，6 -美食，7 -文娱车品，8 -数码家电，9 -男装，10 -内衣，11 -箱包，12 -配饰，13 -户外运动，14 -家装家纺
            'cids' => '',
            //大淘客的二级类目id，通过超级分类API获取。仅允许传一个二级id，当一级类目id和二级类目id同时传入时，会自动忽略二级类目id
            'scids' => '',
            //1-聚划算商品，0-所有商品，不填默认为0
            'juHuaSuan' => 0,
            //1-淘抢购商品，0-所有商品，不填默认为0
            'taoQiangGou' => 0,
            //1-天猫商品，0-所有商品，不填默认为0
            'tmall' => 0,
            //1-天猫超市商品，0-所有商品，不填默认为0
            'tchaoshi' => 0,
            //1-金牌卖家，0-所有商品，不填默认为0
            'goldSeller' => 0,
            //1-海淘商品，0-所有商品，不填默认为0
            'haitao' => '',
            //1-品牌商品，0-所有商品，不填默认为0
            'brand' => 0,
            //当brand传入0时，再传入brandIds将获取不到结果。品牌id可以传多个，以英文逗号隔开，如：”345,321,323”
            'brandIds' => '',
            //价格（券后价）下限
            'priceLowerLimit' => '',
            //价格（券后价）上限
            'priceUpperLimit' => '',
            //最低优惠券面额
            'couponPriceLowerLimit' => '',
            //最低佣金比率
            'commissionRateLowerLimit' => '',
            //最低月销量
            'monthSalesLowerLimit' => '',
            //默认为0，0-综合排序，1-商品上架时间从新到旧，2-销量从高到低，3-领券量从高到低，4-佣金比例从高到低，5-价格（券后价）从高到低，6-价格（券后价）从低到高
            'sort' => '0'
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_dtk_search_goods'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 超级分类
     * @return object
     */
    public function get_super_category(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.1
            'version' => 'v1.0.1',
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_super_category'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 失效商品
     * @return object
     */
    public function get_stale_goods_by_time(): object
    {
        //定义默认请求参数
        $arr = [
            //当前版本： v1.0.1
            'version' => 'v1.0.1',
            //默认为100，可选范围：10,50,100,200，如果小于10按10处理，大于200按照200处理，其它非范围内数字按100处理
            'pageSize' => 100,
            //默认为1，支持传统的页码分页方式和scroll_id分页方式，根据用户自身需求传入值。示例1：商品入库，则首次传入1，后续传入接口返回的pageid，接口将持续返回符合条件的完整商品列表，该方式可以避免入口商品重复；示例2：根据pagesize和totalNum计算出总页数，按照需求返回指定页的商品（该方式可能在临近页取到重复商品）
            'pageId' => '1',
            //商品上架开始时间 请求格式：yyyy-MM-dd HH:mm:ss
            'startTime' => '',
            //商品上架结束时间 请求格式：yyyy-MM-dd HH:mm:ss
            'endTime' => '',
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_stale_goods_by_time'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 9.9包邮精选
     * @return object
     */
    public function op_goods_list(): object
    {
        //定义默认请求参数
        $arr = [
            // 当前版本： v1.0.2
            'version' => 'v1.0.2',
            //默认为100，可选范围：10,50,100,200，如果小于10按10处理，大于200按照200处理，其它非范围内数字按100处理
            'pageSize' => 100,
            //默认为1，支持传统的页码分页方式和scroll_id分页方式，根据用户自身需求传入值。示例1：商品入库，则首次传入1，后续传入接口返回的pageid，接口将持续返回符合条件的完整商品列表，该方式可以避免入口商品重复；示例2：根据pagesize和totalNum计算出总页数，按照需求返回指定页的商品（该方式可能在临近页取到重复商品）
            'pageId' => '1',
            //9.9精选的类目id，分类id请求详情：-1-精选，1 -居家百货，2 -美食，3 -服饰，4 -配饰，5 -美妆，6 -内衣，7 -母婴，8 -箱包，9 -数码配件，10 -文娱车品
            'nineCid' => '-1'
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.op_goods_list'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 各大榜单
     * @return object
     */
    public function get_ranking_list(): object
    {
        //定义默认请求参数
        $arr = [
            // 当前版本： v1.0.2
            'version' => 'v1.0.2',
            //1.实时榜 2.全天榜 3.热推榜（热推榜分类无效）4.复购榜
            'rankType' => 1,
            //大淘客一级类目id
            'cid' => ''
        ];
        //接收请求并合并参数
        $arr = array_merge($arr, input('get.'));
        $data = static::signature(config('api.get_ranking_list'), $arr);
        $data = json_decode($data, true);
        //失败
        if ($data['code'] !== 0) {
            return json([
                'data' => [],
                'code' => -1,
                'msg' => $data['msg']
            ]);
        }
        $nu = input('get.nu/d') === 0 ? 10 : input('get.nu/d');
        $data['data'] = array_slice($data['data'], 0, $nu);
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 推荐栏目
     * @return object
     */
    public function recommended_column(): object
    {
//        //定义默认请求参数
//        $arr = [
//            // 当前版本： v1.0.2
//            'version' => 'v1.0.2',
//            //1.实时榜 2.全天榜 3.热推榜（热推榜分类无效）4.复购榜
//            'rankType' => 1,
//            //大淘客一级类目id
//            'cid' => ''
//        ];
//        //接收请求并合并参数
//        $arr = array_merge($arr, input('get.'));
//        $data = static::signature(config('api.get_ranking_list'), $arr);
//        $data = json_decode($data, true);
//        //失败
//        if ($data['code'] !== 0) {
//            return json([
//                'data' => [],
//                'code' => -1,
//                'msg' => $data['msg']
//            ]);
//        }

        $data['msg'] = '成功';
        $data['data'] = ['美食', '女装', '居家用品', '男装', '数码家电', '鞋品', '母婴', '内衣', '户外运动'];
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

    /**
     * 分类图标
     * @return object
     */
    public function classification_of_icon(): object
    {
//        //定义默认请求参数
//        $arr = [
//            // 当前版本： v1.0.2
//            'version' => 'v1.0.2',
//            //1.实时榜 2.全天榜 3.热推榜（热推榜分类无效）4.复购榜
//            'rankType' => 1,
//            //大淘客一级类目id
//            'cid' => ''
//        ];
//        //接收请求并合并参数
//        $arr = array_merge($arr, input('get.'));
//        $data = static::signature(config('api.get_ranking_list'), $arr);
//        $data = json_decode($data, true);
//        //失败
//        if ($data['code'] !== 0) {
//            return json([
//                'data' => [],
//                'code' => -1,
//                'msg' => $data['msg']
//            ]);
//        }

        $data['msg'] = '成功';
        $data['data'] = [
            [
                'img_url' => 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01s4bCD22JJhxN8pEpi_!!2053469401.png',
                'title' => '9.9包邮',
                'data' => []
            ],
            [
                'img_url' => 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN013HamuH2JJhxN8QOlW_!!2053469401.gif',
                'title' => '疯抢排行',
                'data' => []
            ],
            [
                'img_url' => 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN011U0OZt2JJhxDih0xv_!!2053469401.png',
                'title' => '咚咚抢',
                'data' => []
            ],
            [
                'img_url' => 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01kWR3C62JJhxJYF7om_!!2053469401.png',
                'title' => '商品精选',
                'data' => []
            ]
        ];
        //成功
        return json([
            'data' => $data['data'],
            'code' => 0,
            'msg' => $data['msg']
        ]);
    }

}
