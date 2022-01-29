<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\index;

use app\core\web\Response;

class Main extends BaseController
{
	public function index()
	{
        Response::msg(false,200,"Vpay","欢迎使用，对接（使用）文档等请查看Github README。",-1,"https://github.com/dreamncn/Vpay","Github");
	}
}
