
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

if(localStorage.getItem("notice")===null){
    $("#notice").click();
    localStorage.setItem("notice","1");
}