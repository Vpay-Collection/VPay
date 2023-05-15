<!DOCTYPE html>
<html lang="{$__lang}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="{sprintf('Vpay')}">
    <meta name="keywords" content="{sprintf('Vpay')}"/>
    <title>{$title}</title>
    {include file="layout_headers"}
</head>


<body class="overflow-hidden">


<div id="loading-main-page" style="height: 100vh; width: 100vw" class="" data-immersive-translate-effect="1">
    <div class="loading-mdb loading-spinner position-absolute" data-immersive-translate-effect="1">
        <div class="spinner-border loading-icon" role="status" id="" data-immersive-translate-effect="1"></div>
        <span class="loading-text" data-immersive-translate-effect="1"><font data-immersive-translate-effect="1">Loading...</font></span>
    </div>
    <div class="loading-backdrop position-absolute" id="" style="opacity: 0.4; background-color: rgb(0, 0, 0);"
         data-immersive-translate-effect="1"></div>
</div>


<!-- Container for demo purpose -->
{include file=$__template_file}

</body>


</html>
