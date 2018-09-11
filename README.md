TaobaoConcurrentClient
============
[![GitHub license](https://img.shields.io/github/license/jarod2011/TaobaoConcurrentClient.svg)](https://github.com/jarod2011/TaobaoConcurrentClient/blob/master/LICENSE)

TaobaoConcurrentClient是一个基于[SimpleConcurrentRequestClient](https://github.com/jarod2011/SimpleConcurrentRequestClient)完成的淘宝常用接口项目。目前已支持以下接口

1. CouponInfoRequest [阿里妈妈推广券信息查询](http://open.taobao.com/api.htm?docId=31106&docType=2)
2. ItemInfoRequest [淘宝客商品详情（简版）](http://open.taobao.com/api.htm?docId=24518&docType=2)
3. ItemRecommendRequest [淘宝客商品关联推荐查询](http://open.taobao.com/api.htm?spm=a219a.7386797.0.0.ybRDkn&source=search&docId=24517&docType=2)
4. ItemSearchRequest [淘宝客商品查询](http://open.taobao.com/api.htm?docId=24515&docType=2&source=search)
5. JuTqgListRequest [淘抢购api](http://open.taobao.com/api.htm?spm=a219a.7386797.0.0.2d912cbf8dif1B&source=search&docId=27543&docType=2)
6. PrivilegeRequest [单品券高效转链API](http://open.taobao.com/api.htm?docId=28625&docType=2)
7. ShopInfoRequest [淘宝客店铺查询](http://open.taobao.com/api.htm?source=search&docId=24521&docType=2)
8. UatmEventListRequest [枚举正在进行中的定向招商的活动列表](http://open.taobao.com/api.htm?docId=26449&docType=2&source=search)
9. Url2TklRequest [淘宝客淘口令](http://open.taobao.com/api.htm?source=search&docId=31127&docType=2)
10. TklDecodeRequest [查询解析淘口令](http://open.taobao.com/api.htm?source=search&docId=32461&docType=2)

#### 使用前需要配置接口的app_key app_secret
```php
use SimpleConcurrent\Taobao\TaobaoRequest;

TaobaoRequest::setAppKey(你的APP_KEY);
TaobaoRequest::setAppSecret(你的APP_SECRET);
```
##### 一个简单的使用场景，查询商品信息和默认券
```php
use SimpleConcurrent\Taobao\TBK\CouponInfoRequest;
use SimpleConcurrent\Taobao\TBK\ItemInfoRequest;
use SimpleConcurrent\RequestClient;

$client = new RequestClient();
$couponRequest = CouponInfoRequest::buildRequest($itemId, $couponId);
$infoRequest = ItemInfoRequest::buildRequest($itemId);
$client->addRequest($couponRequest)->addRequest($infoRequest)->promiseAll();

if ($infoRequest->getResponse()->isFail()) {
	printf('查询商品信息失败 %s', $infoRequest->getResponse()->getFail()->getMessage());
} else {
	var_dump($infoRequest->getResponse()->getResult());
}

if ($couponRequest->getResponse()->isFail()) {
	printf('查询优惠券失败 %s', $couponRequest->getResponse()->getFail()->getMessage());
} else {
	var_dump($couponRequest->getResponse()->getResult());
}
```

##### 一个简单的应用场景，查询商品列表，并查询每个商品的默认券
```php
use SimpleConcurrent\RequestClient;
use SimpleConcurrent\Taobao\TBK\ItemSearchRequest;

$client = new RequestClient();
$itemSearchRequest = ItemSearchRequest::fields()->keyword('咖啡')->take(5)->nowpage(2)->onlyTmall()->sortByCommissionRate();

$client->addRequest($itemSearchRequest)->promiseAll();
if ($itemSearchRequest->getResponse()->isFail()) {
    printf('获取商品列表失败 %s', $itemSearchRequest->getResponse()->getFail()->getMessage());
} else {
    $result = $itemSearchRequest->getResponse()->getResult();
    /* 清除上次请求状态 */
    $client->initStatus();
    /* 加入商品默认券的查询 */
    $couponRequest = [];
    foreach ($result['list'] as $item) {
        $couponRequest[$item['num_iid']] = CouponInfoRequest::buildRequest($item['num_iid']);
        $client->addRequest($couponRequest[$item['num_iid']]);
    }
    $client->promiseAll();
    printf("当前共有 %d 个被查到的商品，每页 %d ，共 %d 页，当前第 %d 页列表如下：\n", $result['total'], $result['perpage'], $result['maxpage'], $result['nowpage']);
    foreach ($result['list'] as $item) {
        printf("商品 %d %s | %s \n", $item['num_iid'], $item['title'], ! $couponRequest[$item['num_iid']]->getResponse()->isFail() ? '有券' : '无券');
    }
}
```

