<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * File replace.php
 * Created By ankio.
 * Date : 2023/5/26
 * Time : 22:07
 * Description :
 */
file_put_contents($argv[1],str_replace(<<<EOF
location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
        error_log /dev/null;
        access_log /dev/null;
    }

    location ~ .*\.(js|css)?$
    {
        expires      12h;
        error_log /dev/null;
        access_log /dev/null;
    }
EOF,
'',file_get_contents($argv[1])
));