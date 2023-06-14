<!DOCTYPE html>
<html lang="{$__lang}" style="min-height: 100vh;">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="{sprintf('Vpay')}">
    <meta name="keywords" content="{sprintf('Vpay')}"/>
    <title>{$title}</title>
    {include file="layout_headers"}
</head>


<body class="bg-{$theme} bg-gradient">


<div id="loadingOverlay">
    <div class="loader"></div>
    <p>Loading...</p>
</div>


<!-- Container for demo purpose -->
{include file=$__template_file}

</body>


</html>
