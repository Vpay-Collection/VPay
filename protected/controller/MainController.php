<?php
class MainController extends BaseController {
	// 默认首页

	public function actionIndex(){
        $this->title = "Dreamn收款平台 - V免签";
        $this->layout = "";
        $this->display("index.html");//渲染默认页面输出
        //也可以使用
	}

    public function actionTest()
    {


    }


}
