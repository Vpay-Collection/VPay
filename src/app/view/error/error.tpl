<!DOCTYPE html>
<html lang="{$__lang}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>

    <title>{$title}</title>

    <!-- MDB icon -->
    <link rel="icon" href="../../public/img/mdb-favicon.ico" type="image/x-icon"/>
    <!-- Font Awesome -->
    <!-- Google Fonts Roboto -->
    <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
    />
    <!-- MDB ESSENTIAL -->
    {if $theme == "dark"}
        <link id="theme-link" rel="stylesheet" href="../../public/css/mdb.dark.min.css"/>
    {else}
        <link id="theme-link" rel="stylesheet" href="../../public/css/mdb.min.css"/>
    {/if}
    <!-- MDB PLUGINS -->
    {* <link rel="stylesheet" href="../../public/plugins/css/all.min.css" />*}
    <!-- Custom styles -->
    <script src="../../public/js/theme.js"></script>
</head>

<body>


<!--Main layout-->
<main class="mb-5" style="margin-top: 58px">
    <!-- Container for demo purpose -->
    <div class="container px-4">
        <!-- Grid row -->
        <div class="row">
            <!-- Grid column -->
            <div class="col-12">
                <!--Section: Block Content-->
                <section class="my-5 text-center">
                    <h1 class="display-1">{if $err==':('}{$code}{else}{$title}{/if}</h1>
                    {if $err==':('}
                    <h4 class="mb-4">{$title}</h4>
{/if}
                    <p class="mb-4">
                        {$msg}
                    </p>
                    {if $time!==-1}
                        <a href="{$url}" class="btn btn-primary">
                            {sprintf('%s秒后自动跳转','<span id="jump"></span>')}
                        </a>
                    {else}
                        <a class="btn btn-primary" href="{$url}" role="button">{$desc}</a>
                    {/if}

                </section>
                <!--Section: Block Content-->
            </div>
            <!-- Grid column -->
        </div>
        <!-- Grid row -->

    </div>
    <!-- Container for demo purpose -->
</main>
<!--Main layout-->

<!--Footer-->
<footer></footer>
<!--Footer-->
<!-- End your project here-->
</body>

<!-- Custom scripts -->
<script>
    let wait = "{$time}";

    if (parseInt(wait) !== -1) {
        document.getElementById("jump").innerText = (wait).toString();
        setInterval(function () {
            document.getElementById("jump").innerText = (--wait).toString();
            if (wait <= 0) {
                location.href = "{$url}";
            }
        }, 1000);
    }
</script>

</html>

