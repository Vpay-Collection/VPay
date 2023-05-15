{foreach $shop as $key => $item}
    <div class="p-5 text-center ">
        <h1 class="mb-0 h3">{$key}</h1>
    </div>
    <section class="text-center">
        <div class="row">
            {foreach $item as $item2}
                <div class="col-xl-3 col-md-4  col-sm-6   mb-4 ">
                    <a href="{url('shop','main','item',['id'=>$item2['id']])}" class="text-reset">
                        <div class="card">
                            <div class="bg-image hover-zoom ripple" data-mdb-ripple-color="light">
                                <img src="{$item2['icon']}" class="w-100"/>

                                <div class="mask">
                                    <div class="d-flex justify-content-start align-items-end h-100">
                                        <h5><span class="badge badge-primary ms-2">{$key}</span></h5>
                                    </div>
                                </div>
                                <div class="hover-overlay">
                                    <div class="mask" style="background-color: rgba(251, 251, 251, 0.15)"></div>
                                </div>

                            </div>
                            <div class="card-body">

                                <h4 class="card-title mb-3">{$item2['item_name']}</h4>

                                <h6 class="mb-3 text-danger">￥ <b class="h3">{number_format($item2['item_price'])}</b>
                                </h6>


                            </div>
                        </div>
                    </a>
                </div>
            {/foreach}
        </div>
    </section>
{/foreach}

{include file="layout_scripts"}