//后台api数据
const Api_Ok=0;//接口状态ok
const Api_Err=-1;//接口状态错误
//监控端
const State_Online=1;//监控在线
const State_Offline=0;//监控掉线
const State_Nobind=-1;//监控还没绑定
//递增递减
const PayIncrease=1;//递增
const PayReduce=2;//递减
//订单状态常量定义
const State_Succ = 3;//远程服务器回调成功，订单完成确认
const State_Err = 2;//通知失败,回调服务器没有返回正确的响应信息
const State_Ok = 1;//支付完成，通知成功
const State_Wait = 0;//订单等待支付中
const State_Over = -1;//订单超时
//支付选择
const NeedHtml=1;//需要html
const NeedData=0;//我只要支付相关的数据
//支付方式
const PayWechat=1;
const PayAlipay=2;