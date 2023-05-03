<!-- Container for demo purpose -->
<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">

        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">{lang("用户总数")}</p>
                        <h2 class="mb-0 text-primary">{$user_count}</h2>

                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">{lang("邮件总数")}</p>
                        <h2 class="mb-0 text-primary">{$mail_count}</h2>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">{lang("APP总数")}</p>
                        <h2 class="mb-0 text-primary">{$app_count}</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Section: Design Block -->

</div>
<!-- Container for demo purpose -->

{include file="layout_scripts"}
