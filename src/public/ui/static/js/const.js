//后台api数据
const ApiOk=0;//接口状态ok
const ApiError=-1;//接口状态错误
//监控端
const AppOnline=1;//监控在线
const AppOffline=0;//监控掉线
const AppNoBind=-1;//监控还没绑定
//递增递减
const PayIncrease=1;//递增
const PayReduce=2;//递减
//订单状态常量定义
const StateSuccess = 3;//远程服务器回调成功，订单完成确认
const StateError = 2;//通知失败,回调服务器没有返回正确的响应信息
const StateOk = 1;//支付完成，通知成功
const StateWait = 0;//订单等待支付中
const StateOver = -1;//订单超时
//支付选择
const NeedHtml=1;//需要html
const NeedData=0;//我只要支付相关的数据
//支付方式
const PayWechat=1;
const PayAlipay=2;