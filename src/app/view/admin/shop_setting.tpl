<link rel="stylesheet" href="../../public/app/dist/ui/trumbowyg.min.css?v={$__version}" media="none"
      onload="this.media='all'">
<link rel="stylesheet" href="../../public/app/dist/plugins/colors/ui/trumbowyg.colors.min.css?v={$__version}"
      media="none"
      onload="this.media='all'">
<div class="container my-5 py-5">

    <!--Section: Profile-->
    <section class="mb-10">
        <div class="row">

            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3">
                        <strong>内置商城配置</strong>
                    </div>
                    <div class="card-body ">

                        <form action="">
                            <div class="form-check mb-4 text-start">
                                <input class="form-check-input" type="checkbox" value="1"
                                       {if $state}checked="checked"{/if} name="state" id="state"/>
                                <label class="form-check-label"
                                       for="state">开启商城并将首页重定向到商城</label>
                            </div>
                            <div class="form-outline  mb-4 ">
                                <input type="text" name="title" id="title" class="form-control"
                                       value="{$title}"/>
                                <label class="form-label" for="title">商城名称</label>
                            </div>
                            <div class=" mb-4 ">
                                <div id="trumbowyg">
                                    {$notice nofilter}
                                </div>
                            </div>


                            <button type="submit" class="btn btn-primary mb-2" id="save">
                                保存
                            </button>

                        </form>
                    </div>
                </div>
            </div>


        </div>
    </section>
    <!--Section: Profile-->

</div>


{include file="layout_scripts"}
<script src="../../public/app/dist/trumbowyg.min.js?v={$__version}" defer></script>
<script src="../../public/app/dist/langs/zh_cn.min.js?v={$__version}" defer></script>
<script src="../../public/app/dist/plugins/colors/trumbowyg.colors.min.js?v={$__version}" defer></script>
<script src="../../public/app/shop_setting.js?v={$__version}" defer></script>