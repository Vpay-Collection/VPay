<!--Section: Design Block-->
<section class="pt-5 mb-10">
    <div class="row">
        <div class=" col-lg-5 col-xl-5 pe-lg-4  mb-5 mb-lg-0">
            <div class="p-3  card">
                <div class="lightbox ">
                    <img src="{$icon}"
                         alt="{$item_name}" class="ecommerce-gallery-main-img active w-100 h-auto"/>
                </div>
                <h2 class="fw-bold mb-3">{$item_name}</h2>
                <p class="fs-5 mb-4" href="#!"><strong class="text-danger">￥ {number_format($item_price)}</strong></p>

                <div class="pt-2" style="max-width: 500px;">
                    <form id="buy">
                        <div class="form-outline mb-4">
                            <input type="text" class="form-control" id="mail" name="mail"/>
                            <label class="form-label" for="mail">邮箱</label>
                        </div>
                        {foreach $inputs as $input}
                            {if substr($input,-1)=="_"}
                                {$input = substr($input, 0, -1)}
                                <div class="form-outline mb-4">
                                    <textarea class="form-control" id="{$input}"
                                              name="{$input}">{if isset($args[$input])}{$args[$input]}{/if}</textarea>
                                    <label class="form-label" for="{$input}">{$input}</label>
                                </div>
                            {else}
                                <div class="form-outline mb-4">
                                    <input type="text" class="form-control" id="{$input}" name="{$input}"
                                           value="{if isset($args[$input])}{$args[$input]}{/if}"/>
                                    <label class="form-label" for="{$input}">{$input}</label>
                                </div>
                            {/if}

                        {/foreach}
                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <button class="btn btn-primary me-2" data-id="{$id}" data-type="1"><i
                                        class="fab fa-alipay  me-2"></i>支付宝
                            </button>
                            <button class="btn btn-success me-2" data-id="{$id}" data-type="2"><i
                                        class="fab fa-weixin me-2"></i>微信
                            </button>
                            <button class="btn btn-info" data-id="{$id}" data-type="3"><i class="fab fa-qq me-2"></i>QQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="  col-lg-7 col-xl-7 ps-lg-4">
            <div class="card">


                <div class="p-3">
                    {$description_nofilter nofilter}
                </div>
            </div>

        </div>
    </div>
</section>
<!--Section: Design Block-->
{include file="layout_scripts"}

<script src="../../public/app/shop_details.js?v={$__version}" defer></script>