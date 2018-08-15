<!doctype html>
<!--[if IE 9 ]><html class="ie9" lang="en" ng-app="mallBd"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="en" ng-app="mallBd"><!--<![endif]-->

@include('web/partial/header/mainStartFacebook')
@include('web/partial/header/cartScript')
@include('web/partial/header/mainEnds')
<!--zoomer include-->
<link href="{{asset('/template_resource/elevatezoom/jquery.fancybox.css')}}" rel="stylesheet">

<body ng-controller="CartController">
    @include('web/partial/loader')
    <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId=1648526795437995";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
    
<!--wide layout-->
<div class="wide_layout relative">
    <!--[if (lt IE 9) | IE 9]>
    <div style="background:#fff;padding:8px 0 10px;">
        <div class="container" style="width:1170px;">
            <div class="row wrapper">
                <div class="clearfix" style="padding:9px 0 0;float:left;width:83%;"><i
                        class="fa fa-exclamation-triangle scheme_color f_left m_right_10"
                        style="font-size:25px;color:#e74c3c;"></i><b style="color:#e74c3c;">Attention! This page may not
                    display correctly.</b> <b>You are using an outdated version of Internet Explorer. For a faster, safer
                    browsing experience.</b></div>
                <div class="t_align_r" style="float:left;width:16%;"><a
                        href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"
                        class="button_type_4 r_corners bg_scheme_color color_light d_inline_b t_align_c" target="_blank"
                        style="margin-bottom:2px;">Update Now!</a></div>
            </div>
        </div>
    </div>
    <![endif]-->
    <!--markup header-->
    <header role="banner" class="type_5 fixed-top">


        @include('web/partial/header/top')
                <!--header bottom part-->
        @include('web/partial/new_menu')

    </header>
    <div class="other-free-gap">

    </div>
    <?php
            $linkStr ="";
            $count=1;
        ?>
<!--breadcrumbs-->
 @include('web/partial/sideblock/shippingtext')
<section class="breadcrumbs">
    <div class="container">
        <ul class="horizontal_list clearfix bc_list f_size_medium">
            <li class="m_right_10"><a href="{{url('')}}" class="default_t_color">Home<i
                    class="fa fa-angle-right d_inline_middle m_left_10"></i></a></li>
             <?php if(isset($categoryNameList) && $categoryNameList !=null){ ?>       
            @foreach($categoryNameList as $key=>$categoryName)
                <?php

                    $tmpStr=$categoryName->id."/";
                    if($count==1)
                    {
                        $linkStr = $categoryName->id."/";

                    }
                    else{
                        $linkStr .= $tmpStr;
                    }

                $count++;                
                ?>
                <?php if($categoryName->id >0){ ?>
                <li class="m_right_10">
                       <a href="{{url("category/$linkStr")}}" class="default_t_color">{{$categoryName->title }}
                          <i class="fa fa-angle-right d_inline_middle m_left_10"></i>
                       </a>
                   </li>
                
                <?php   } ?>
             @endforeach
             <?php   } ?>
             <li class="m_right_10  current">
                    <span style="color: #111;">{{ $product->title }}                      
                    </span>
              </li>
        </ul>
    </div>
</section>
<!--<section class="free-shipping">
    <p><i class="fa fa-truck"></i>FREE SHIPPING on 500tk Purchase</p>
</section>-->

    <!--Quick view product -->

    <!--content-->
    <div class="page_content_offset">
        <div class="container">
            <div class="row clearfix">
                <!--left content column-->
                <section class="col-lg-9 col-md-9 col-sm-9 m_xs_bottom_30">
                    <?php
                    if($product->id == 0){ ?>
                       <div class="row clearfix text-center">
                        <img src="{{asset('/template_resource/images/box.png')}}" />
                        <p class="error-not-found">Hmm,Something went wrong. Product Not found</p>
                      </div>
                    <?php }else{
                    ?>
                    <div class="clearfix m_bottom_30 t_xs_align_c">
                        <div class="photoframe type_2 shadow r_corners f_left f_sm_none d_xs_inline_b product_single_preview relative m_right_30 m_bottom_5 m_sm_bottom_20 m_xs_right_0 w_mxs_full" style="float: left; margin-right: 0px;">
                            <!--<span class="hot_stripe"><img src="{{asset('/template_resource/images/sale_product.png')}}" alt=""></span>-->
                            
                            
                                                        <?php 
//                                                        $filterDate = date('Y-m-d', strtotime(' -20 day'));
                                                        if($product->quantity <= 0){ ?>
                                                        <div class="o-tag tag-detail">
                                                                    <span>hot</span>
                                                         </div>
                                                        <?php                                                        
                                                        }else{                                                           
                                                            if (($product->isFeatured)) {
                                                                ?>
                                                                <div class="f-tag tag-detail">
                                                                    <span>hot</span>
                                                                </div>
                                                                <?php
                                                            } else if ($product->discountActiveFlag) {
                                                                ?>
                                                                <div class="s-tag tag-detail">
                                                                    <span>Sale</span>
                                                                </div>
                                                                <?php
                                                            } else if ($product->isBestSeller) {
                                                                ?>
                                                                <div class="b-tag tag-detail">
                                                                    <span>Best Seller</span>
                                                                </div>
                                                            <?php } else { if($product->createdOn >$filterDate){ ?>
                                                                <div class="h-tag tag-detail">
                                                                    <span>New</span>
                                                                </div>
                                                            <?php
                                                            }
                                                        } 
                                                        
                                                        } 
                                                        ?>
                            <div class="relative d_inline_b m_bottom_10 qv_preview d_xs_block " style="border:1px solid #eee">
                                    
                                @if($product->pictures== NULL)
									<img id="product" src="http://placehold.it/360x360?text=Product Banner not found"/>
								@else
								@foreach($product->pictures as $pic)
                                    @if($pic->cover==1)
                                     <img id="product" src="{{ $imageUrl }}product/pagelarge/{{$pic->name}}" data-zoom-image="{{ $imageUrl }}product/large/{{$pic->name}}" onerror="this.src='http://placehold.it/360x360?text=Product Banner not found'"/>
                                     @endif
                                     
                               @endforeach
                            @endif   
                            </div>
                            <!--carousel-->
                            <div class="relative qv_carousel_wrap">
                                <button class="button_type_11 bg_light_color_1 t_align_c f_size_ex_large bg_cs_hover r_corners d_inline_middle bg_tr tr_all_hover qv_btn_single_prev">
                                    <i class="fa fa-angle-left "></i>
                                </button>

                                <div class="qv_carousel_single d_inline_middle" id="productgallery">
 
                                    @foreach($product->pictures as $pic)
                                        @if($pic->cover==1)
                                            <a href="#" data-image="{{ $imageUrl }}product/pagelarge/{{@$pic->name}}" data-zoom-image="{{ $imageUrl }}product/large/{{@$pic->name}}">
                                            <img id="product" src="{{ $imageUrl }}product/thumbnail/{{@$pic->name}}" />
                                          </a>
                                        @endif

                                    @endforeach
                                    @foreach($product->pictures as $pic)
                                        @if($pic->cover!=1)
                                            <a href="#" data-image="{{ $imageUrl }}product/pagelarge/{{@$pic->name}}" data-zoom-image="{{ $imageUrl }}product/large/{{@$pic->name}}">
                                            <img id="product" src="{{ $imageUrl }}product/thumbnail/{{@$pic->name}}" />
                                          </a>
                                        @endif

                                    @endforeach

                                  </div>
                                    
                                
                                <button class="button_type_11 bg_light_color_1 t_align_c f_size_ex_large bg_cs_hover r_corners d_inline_middle bg_tr tr_all_hover qv_btn_single_next">
                                    <i class="fa fa-angle-right "></i>
                                </button>
                            </div>
                        </div>
<!--                        <div class="p_top_10 t_xs_align_l pop-desc" style="float: right; width: 360px;">
                            description
                            <h2 class="color_dark fw_medium m_bottom_10 pop-title">{{ $product->title }}</h2>

                            <div class="m_bottom_10">
                                rating
                                <ul class="horizontal_list d_inline_middle type_2 clearfix rating_list tr_all_hover">
                                    <?php
                                    $avgRating = ceil($product->avgRating);
                                    if($avgRating>5)
                                    {
                                        $avgRating=5;
                                    }

                                    $left = 5-$avgRating;
                                    ?>
                                    @for($i=0;$i<$avgRating;$i++)
                                        <li class="active">
                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                            <i class="fa fa-star active tr_all_hover"></i>
                                        </li>
                                    @endfor
                                    @if($left>0)
                                        @for($i=0;$i<$left;$i++)
                                            <li>
                                                <i class="fa fa-star-o empty tr_all_hover"></i>
                                                <i class="fa fa-star active tr_all_hover"></i>
                                            </li>
                                        @endfor
                                    @endif
                                </ul>
                                <a href="#" class="d_inline_middle default_t_color f_size_small m_left_5"><span id="reviewCount{{$product->id}}"></span> Review(s) </a>
                            </div>
                            <hr class="m_bottom_10 divider_type_3">
                            <table class="description_table m_bottom_10 pop-table">
                                <tr>
                                    <td>Manufacturer:</td>
                                    <td class="td-last"><a href="#" class="color_dark">{{ $product->manufacturer->name }}</a></td>
                                </tr>
                                <tr>
                                    @if($product->quantity>0)
                                        <td>Availability:</td>
                                        <td class="td-last"><span class="color_green">in stock </span>{{$product->quantity}} item(s)</td>
                                    @else
                                        <td>Availability:</td>
                                        <td class="td-last"><span class="colorpicker_rgb_r"><b style="color: red;">Out of stock</b></span>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Product Code:</td>
                                    <td class="td-last">{{ $product->code }}</td>
                                </tr>
                            </table>
                            
                            <hr class="divider_type_3 m_bottom_10">
                            <p class="m_bottom_10"> <?php //print $product->description; ?></p>
                            <hr class="divider_type_3 m_bottom_15 m_top_5">
                            <div class="m_bottom_15">
                                {{--<s class="v_align_b f_size_ex_large"><span>&#2547</span>152.00</s>--}}
                               {{-- <span class="v_align_b f_size_big m_left_5 scheme_color fw_medium"><span>&#2547</span>{{ @$product->prices[0]->retailPrice }}</span>--}}
                                @if($product->discountActiveFlag)
                                    <s class="v_align_b f_size_ex_large pop-old"><span><?php// echo $currency->HTMLCode; ?></span>{{ number_format($product->prices[0]->retailPrice,2)}}</s><span
                                            class="v_align_b f_size_big m_left_5 scheme_color fw_medium  pop-price"><span><?php// echo $currency->HTMLCode; ?></span>{{ number_format(($product->prices[0]->retailPrice-$product->discountAmount),2)}}</span>
                                @else
                                    <span
                                            class="v_align_b f_size_big m_left_5 scheme_color fw_medium pop-price"><span><?php //echo $currency->HTMLCode; ?></span>{{ number_format($product->prices[0]->retailPrice,2)}}</span>

                                @endif

                            </div>
                            <table class="description_table type_2 m_bottom_15">
                                @foreach(@$product->attributes as $attributes)
                                    <tr>
                                        <td class="v_align_m">{{$attributes->name}}:</td>
                                        <td class="v_align_m">
                                            {{--<div class="custom_select f_size_medium relative d_inline_middle">--}}
                                            {{--<div class="select_title r_corners relative color_dark">Pick</div>--}}
                                            {{--<ul class="select_list d_none"></ul>--}}


                                            {{--</div>--}}
                                            <select name="product_name" id="select_attribute_{{$product->id}}_{{$attributes->id}}">
                                                @foreach(@$attributes->attributesValue as $attributesValue)
                                                    <option value="{{@$attributesValue->id}}">{{@$attributesValue->value}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="v_align_m" style="font-size: 15px;color: #333;">Quantity:</td>
                                    <td class="v_align_m">
                                        <div class="clearfix quantity r_corners d_inline_middle f_size_medium color_dark">
                                            <button class="bg_tr d_block f_left" data-direction="down">-</button>
                                            <input id="quantity_modifier_{{$product->id}}"type="text" name="" readonly value="1" class="f_left">
                                            <button class="bg_tr d_block f_left" data-direction="up">+</button>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="pull-left">
                                {{--@if(Auth::check())--}}
                                <div class="d_ib_offset_0 m_bottom_20 position-relative">
                                    <input type="hidden" id="productJsonObj_{{$product->id}}" value="{{json_encode($product)}}"  />
                                    @if(@$product->quantity>0)
                                        <button class="button_type_12 r_corners bg_scheme_color color_light tr_delay_hover d_inline_b f_size_large add-busket" ng-click="addToCart('productJsonObj_{{$product->id}}',-1,true)">
                                            Add to Basket
                                        </button>
                                    @endif
                                    
                                    <div class="abs-loader" id="load-img{{ $product->id }}" hidden></div>
                                    @if($product->isWished)
                                        <button id="inner{{ $product->id }}" class="active-wishlist button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 fav-btn" onclick="submitWishListforproductdetails({{$product->id}})">
                                            <span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span><i
                                                    class="fa fa-heart-o f_size_big"></i></button>
                                    @else
                                        <button id="inner{{ $product->id }}" class="button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 fav-btn" onclick="submitWishListforproductdetails({{$product->id}})">
                                            <span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span><i
                                                    class="fa fa-heart-o f_size_big"></i></button>
                                    @endif
                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 compare-btn" onclick="compare_product('{{$product->id}}');">
                                        <span class="tooltip tr_all_hover r_corners color_dark f_size_small">Compare</span><i
                                                class="fa fa-files-o f_size_big"></i></button>
                                    <button class="button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 relative ques-btn">
                                        <i class="fa fa-question-circle f_size_big"></i><span
                                                class="tooltip tr_all_hover r_corners color_dark f_size_small">Ask a Question</span></button>
                                    <div class="notify-small" id="addnotify{{ $product->id }}"  hidden><span>Product Added</span></div>
                                    <div class="notify-small" id="addnotify2{{ $product->id }}"  hidden><span>Already Added</span></div>
                                </div>
                               {{-- @else
                                    <div class="d_ib_offset_0 m_bottom_20 position-relative">
                                        <input type="hidden" id="productJsonObj_{{$product->id}}" value="{{json_encode($product)}}"  />
                                        <button class="button_type_12 r_corners bg_scheme_color color_light tr_delay_hover d_inline_b f_size_large add-busket" ng-click="addToCart('productJsonObj_{{$product->id}}',-1,true)">
                                            Add to Basket
                                        </button>
                                        <div class="abs-loader" id="load-img{{ $product->id }}" hidden></div>
                                        <button id="inner{{ $product->id }}" class="button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 fav-btn" onclick="submitWishListforproductdetails({{$product->id}})">
                                            <span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span><i
                                                    class="fa fa-heart-o f_size_big"></i></button>
                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 compare-btn" onclick="compare_product('{{$product->id}}');">
                                            <span class="tooltip tr_all_hover r_corners color_dark f_size_small">Compare</span><i
                                                    class="fa fa-files-o f_size_big"></i></button>
                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover d_inline_b r_corners color_dark m_left_5 p_hr_0 relative ques-btn">
                                            <i class="fa fa-question-circle f_size_big"></i><span
                                                    class="tooltip tr_all_hover r_corners color_dark f_size_small">Ask a Question</span></button>
                                        <div class="notify-small" id="loginnotify{{ $product->id }}"  hidden><span>Please Login First</span></div>
                                    </div>
                                @endif--}}

                                <p class="d_inline_middle" style="display: none;">Share this:</p>

                                <div class="d_inline_middle m_left_5 addthis_widget_container" style="display: none;">
                                     AddThis Button BEGIN 
                                    <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                        <a class="addthis_button_preferred_1"></a>
                                        <a class="addthis_button_preferred_2"></a>
                                        <a class="addthis_button_preferred_3"></a>
                                        <a class="addthis_button_preferred_4"></a>
                                        <a class="addthis_button_compact"></a>
                                        <a class="addthis_counter addthis_bubble_style"></a>
                                    </div>
                                     AddThis Button END 
                                </div>
                            </div>

                        </div>-->

                        <!--start tanveer new html code here-->
                        <div class="p_top_10 t_xs_align_l pop-desc pro_detail" style="">
                            <p class="manf-name"><a style="cursor: pointer;" onclick="productByManufacturer('{{$product->manufacturer->id}}');">{{$product->manufacturer->name}}</a></p>
                            <p class="prod-name-detail">{{$product->title}}</p>
                            @if($product->discountActiveFlag)
                                <p style="color:red;">Your Save<span><?php echo $currency->HTMLCode; ?> {{ number_format($product->discountAmount,2)}} </span></p>
                             @endif
                            <p class="detail-price">
                                @if($product->discountActiveFlag)
                                <span class="older"><?php echo $currency->HTMLCode; ?> {{ number_format($product->prices[0]->retailPrice,2)}}</span><span class="newer"><?php echo $currency->HTMLCode; ?> {{ number_format(($product->prices[0]->retailPrice-$product->discountAmount),2)}}</span>
                                @else
                                <span class="newer"><?php echo $currency->HTMLCode; ?> {{ number_format($product->prices[0]->retailPrice,2)}}</span>
                                @endif
                                
                            </p>
                            <span class="detail-desc"><?php echo $product->description; ?></span>
                            <input type="hidden" id="productJsonObj_{{$product->id}}" value="{{json_encode($product)}}"  />
                            <p class="rate-it">
                                <ul class="horizontal_list d_inline_middle type_2 clearfix rating_list tr_all_hover">
                                    <?php
                                    $avgRating = ceil($product->avgRating);
                                    if($avgRating>5)
                                    {
                                        $avgRating=5;
                                    }

                                    $left = 5-$avgRating;
                                    ?>
                                    @for($i=0;$i<$avgRating;$i++)
                                        <li class="active">
                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                            <i class="fa fa-star active tr_all_hover"></i>
                                        </li>
                                    @endfor
                                    @if($left>0)
                                        @for($i=0;$i<$left;$i++)
                                            <li>
                                                <i class="fa fa-star-o empty tr_all_hover"></i>
                                                <i class="fa fa-star active tr_all_hover"></i>
                                            </li>
                                        @endfor
                                    @endif
                                     <li>
                                         <span class="rate_number">{{$avgRating}}</span>
                                     </li>   
                                </ul>
                            <p class="write-review desc-other-block"><a onclick="reviewandcomment(3)" style="cursor: pointer;">Write a review</a></p>
                            
                             </p>
                            
                            <p class="desc-other-block">
                                <a onclick="reviewandcomment(1)" style="cursor: pointer;">Add a Comment</a>
                                
                                @if($product->isWished)
                                          <a class="favourite-link active" style="cursor: pointer;" onclick="submitWishListforproductdetails({{$product->id}})"><i class="fa fa-heart"></i>Add to wishlist</a>              
                                 @else
                                    <a class="favourite-link" style="cursor: pointer;" onclick="submitWishListforproductdetails('{{$product->id}}','quick_view_product_{{$product->id}}')"><i class="fa fa-heart-o favourite_{{$product->id}}"></i>Add to wishlist</a>    
                                @endif
                                <a class="wishlist-link" onclick="compare_product('{{$product->id}}');" style="cursor: pointer;"><i class="fa fa-copy"></i>Compare Product</a>
                                
                            </p>
                            <div id="scroll_div"></div>
                            <div class="notify-small" id="outeraddnotify{{ $product->id }}"  hidden><span>Product Added</span></div>
                             <div class="notify-small" id="outeraddnotify2{{ $product->id }}"  hidden><span>Already Added</span></div>
                                                    @foreach(@$product->attributes as $attributes)
                                                       <div class="add-qn" style="margin-bottom: 25px;">
                                                            <div class="col-md-4">
                                                                {{$attributes->name}}:
                                                            </div>
                                                            <div class="col-md-8">
                                                               <select class="qnty" style="width: 90%;" name="product_name" id="select_attribute_{{$product->id}}_{{$attributes->id}}">
                                                                        @foreach(@$attributes->attributesValue as $attributesValue)
                                                                        <option value="{{@$attributesValue->id}}">{{@$attributesValue->value}}</option>
                                                                        @endforeach
                                                               </select>
                                                            </div>
                                                    
                                                        </div>
                                                            @endforeach
                            <div class="add-qn">
                                <div class="col-md-4">
                                    <select class="qnty q_selector" id="quantity_modifier_{{$product->id}}">
                                        @if($product->quantity>0)
                                          @for($i=$product->minimumOrderQuantity;$i<=$product->quantity;$i++)
                                            <option value="{{$i}}">Qty: {{$i}}</option>
                                            @endfor
                                        @else
                                           <option value="0">Qty- 0</option> 
                                       @endif
                                       
                                        
                                    </select>
                                </div>
                                <div class="col-md-8">
                                     @if($product->quantity>0)
                                          <button class="btn-new-ad" ng-click="addToCart('productJsonObj_{{$product->id}}',-1,true)">Add to basket</button>
                                     @else
                                          <button class="btn-new-ad" style="color: red;">Out Of Stock</button>
                                     @endif
                                    
                                </div>
                            </div>
                        </div>
                        <!--End tanveer new html code here-->
                    </div>
                    <!--tabs-->
                    <div class="tabs m_bottom_45">
                        <!--tabs navigation-->
                        <nav>
                            <ul class="tabs_nav horizontal_list clearfix">
                                
                                <li class="f_xs_none"><a href="#tab-1" class="bg_light_color_1 color_dark tr_delay_hover r_corners d_block">Description</a>
                                </li>
                                <li class="f_xs_none"><a href="#tab-2" id="tab-load-1" class="bg_light_color_1 color_dark tr_delay_hover r_corners d_block">Comments</a>
                                </li>
                                <li class="f_xs_none"><a href="#tab-3" id="tab-load-3" class="bg_light_color_1 color_dark tr_delay_hover r_corners d_block">Reviews</a>
                                </li>
                                <!--<li class="f_xs_none"><a href="#tab-4" class="bg_light_color_1 color_dark tr_delay_hover r_corners d_block">Product Video</a></li>-->
                                
                                <li class="f_xs_none"><a href="#tab-4" class="bg_light_color_1 color_dark tr_delay_hover r_corners d_block">Others</a></li>
                                
                            </ul>
                        </nav>
                        <section class="tabs_content shadow r_corners">
                            <div id="tab-1">
                                <?php if($product->videoLinkWeb != null){ ?>
                                <div class="iframe_video_wrap">
                                    <iframe src="{{$product->videoLinkWeb}}"></iframe>
                                </div>
                                <br><br>
                                <?php } ?>
                                <?php echo $product->longDescription; ?>
                                
                            </div>
                            <div id="tab-2">
                                <div class="fb-comments" data-href="{{url('/product/' . $product->url . '/' . $product->code)}}" data-numposts="5"></div>
                                
                            </div>
                            
                            <div id="productId" hidden>{{$product->id}}</div>
                            <!--<div id="offset" hidden>0</div>-->
                            <div id="limit" hidden>3</div>
                            <div id="tab-3">
                                <div class="row clearfix">
                                    <div class="col-lg-8 col-md-8 col-sm-8" >
                                        <div class="row clearfix review-block">
                                            <div class="rloader-container" id="reviewLoader" hidden>
                                                <div id="fountainG">
                                                    <div id="fountainG_1" class="fountainG"></div>
                                                    <div id="fountainG_2" class="fountainG"></div>
                                                    <div id="fountainG_3" class="fountainG"></div>
                                                    <div id="fountainG_4" class="fountainG"></div>
                                                    <div id="fountainG_5" class="fountainG"></div>
                                                    <div id="fountainG_6" class="fountainG"></div>
                                                    <div id="fountainG_7" class="fountainG"></div>
                                                    <div id="fountainG_8" class="fountainG"></div>
                                                </div>
                                            </div>
                                            <div id="offset" hidden>1</div>
                                            <h5 class="fw_medium m_bottom_15">Last Reviews</h5>
                                            <div class="col-md-12" id="reviews">

                                            </div>
                                            <center>
                                                <input type="button" value="Load More" id="loaderbtn" onclick="loadMoreReviews()"/>
                                            </center>
                                        </div>

                                        <!--review-->
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <h5 class="fw_medium m_bottom_15">Write a Review</h5>

                                        

                                        <form>
                                            <p class="f_size_medium m_bottom_5">First: Rate the product. Please select a rating between 0 (poorest)
                                                and 5 stars (best).</p>

                                            <div class="d_block full_width m_bottom_10">
                                                <div class="d_block m_bottom_5 v_align_m">
                                                    <p class="f_size_medium d_inline_middle m_right_5">Rating:</p>
                                                    <!--rating-->
                                                    <ul id="reviewPoint" class="horizontal_list clearfix rating_list type_2 d_inline_middle">
                                                        <li>
                                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                                            <i class="fa fa-star active tr_all_hover"></i>
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                                            <i class="fa fa-star active tr_all_hover"></i>
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                                            <i class="fa fa-star active tr_all_hover"></i>
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                                            <i class="fa fa-star active tr_all_hover"></i>
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                                            <i class="fa fa-star active tr_all_hover"></i>
                                                        </li>
                                                    </ul>
                                                </div>
                                                
                                            </div>
                                            <p class="f_size_medium m_bottom_15">Now please write a  review....</p>
                                            <textarea class="r_corners full_width m_bottom_10 review_tarea" id="note"></textarea>

                                            
                                            <input type="button" class="r_corners button_type_4 tr_all_hover mw_0 color_dark bg_light_color_2" value="Submit" onclick="submitReview({{$product->id}})">
                                                <div id="notification" align="center" ></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div id="tab-4">
                                <h5 class="fw_medium m_bottom_10"> Dimensions and Weight</h5>
                                <table class="description_table m_bottom_5">
                                    <tr>
                                        <td>Product Length:</td>
                                        <td><span class="color_dark"><?php if($product->depth != ''){ echo $product->depth . ' M';} else{ echo 'N/A'; } ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Product Width:</td>
                                        <td><span class="color_dark"><?php if($product->width != ''){ echo $product->width . ' M';} else{ echo 'N/A'; } ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Product Height:</td>
                                        <td><span class="color_dark"><?php if($product->height != ''){ echo $product->height . ' M';} else{ echo 'N/A'; } ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Product Weight:</td>
                                        <td><span class="color_dark"><?php if($product->weight != ''){ echo $product->weight . ' KG';} else{ echo 'N/A'; } ?></span></td>
                                    </tr>
                                </table>
                                
                            </div>
                        </section>

                    </div>
                    <div class="clearfix">
                        <h2 class="color_dark tt_uppercase f_left m_bottom_15 f_mxs_none">Related Products</h2>

                        <div class="f_right clearfix nav_buttons_wrap f_mxs_none m_mxs_bottom_5">
                            <button class="button_type_7 bg_cs_hover box_s_none f_size_ex_large t_align_c bg_light_color_1 f_left tr_delay_hover r_corners rp_prev">
                                <i class="fa fa-angle-left"></i></button>
                            <button class="button_type_7 bg_cs_hover box_s_none f_size_ex_large t_align_c bg_light_color_1 f_left m_left_5 tr_delay_hover r_corners rp_next">
                                <i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                    <?php
                    $countPic =0;
                    $productCount = 0;
                    ?>
                    <div class="related_projects m_bottom_15 m_sm_bottom_0 m_xs_bottom_15">

                        @foreach($relatedProducts as $relatedProduct)


                            <figure class="r_corners photoframe shadow relative d_xs_inline_b tr_all_hover">
                                <?php 
                                                        if($relatedProduct->quantity <= 0){ ?>
                                                        <div class="o-tag">
                                                                    <span>hot</span>
                                                         </div>
                                                        <?php                                                        
                                                        }else{                                                           
                                                            if (($relatedProduct->isFeatured)) {
                                                                ?>
                                                                <div class="f-tag">
                                                                    <span>hot</span>
                                                                </div>
                                                                <?php
                                                            } else if ($relatedProduct->discountActiveFlag) {
                                                                ?>
                                                                <div class="s-tag">
                                                                    <span>Sale</span>
                                                                </div>
                                                                <?php
                                                            } else if ($relatedProduct->isBestSeller) {
                                                                ?>
                                                                <div class="b-tag">
                                                                    <span>Best Seller</span>
                                                                </div>
                                                            <?php } else { if($relatedProduct->createdOn >$filterDate){ ?>
                                                                <div class="h-tag">
                                                                    <span>New</span>
                                                                </div>
                                                            <?php
                                                            }
                                                        } 
                                                        
                                                        } 
                                                        ?>
                                {{--<span class="hot_stripe type_2"><img src="{{asset('/template_resource/images/hot_product_type_2.png')}}" alt=""></span>--}}
                                <!--product preview-->
                                <a href="{{url('/product/' . $relatedProduct->url . '/' . $relatedProduct->code)}}" class="d_block relative pp_wrap">
                                    <!--hot product-->
                                    <img src="{{$imageUrl.'product/general/'.@$relatedProduct->pictures[0]->name}}" onerror="this.src='http://placehold.it/242x242'" class="tr_all_hover" alt="">
                                    {{--<span id="quick_view_product_{{$relatedProduct->id}}_trigger_btn" data-popup="#quick_view_product_{{$relatedProduct->id}}" class="box_s_none button_type_5 color_light r_corners tr_all_hover d_xs_none" onclick="numberOfReview('{{$relatedProduct->id}}')">Quick View</span>--}}

                                </a>
                                <!--description and price of product-->
                                <?php
                                $length = strlen($relatedProduct->title);
                                if($length>=19)
                                {
                                    $editedTitle = substr($relatedProduct->title, 0, 18)."...";
                                }
                                else{
                                    $editedTitle = $relatedProduct->title;
                                }

                                ?>

                                <figcaption class="t_xs_align_l">
                                    <h5 class="m_bottom_10 prod_name_fix text-center"><a href="{{url('/product/' . $relatedProduct->url . '/' . $relatedProduct->code)}}" class="color_dark">
                                            {{ @$editedTitle }}
                                        </a></h5>

                                    <div class="clearfix">
                                        <p class="scheme_color f_size_large f_left item-redesign-price">
                                        @if($relatedProduct->discountActiveFlag)
                                            <s class="v_align_b f_size_ex_large price-old"><span><?php echo $currency->HTMLCode; ?></span>{{number_format($relatedProduct->prices[0]->retailPrice,2)}}</s><span
                                                class="v_align_b f_size_big m_left_5 scheme_color fw_medium price-usual"><span><?php echo $currency->HTMLCode; ?></span>{{ number_format(($relatedProduct->prices[0]->retailPrice-$relatedProduct->discountAmount),2)}}</span></p>
                                        @else
                                            <span
                                                    class="v_align_b f_size_big m_left_5 scheme_color fw_medium price-usual"><span><?php echo $currency->HTMLCode; ?></span>{{number_format($relatedProduct->prices[0]->retailPrice,2)}}</span>

                                            @endif
                                                    <!--rating-->

                                            <ul class="horizontal_list f_right clearfix rating_list tr_all_hover rating-item">
                                                <?php
                                                $avgRating2 = ceil($relatedProduct->avgRating);
                                                if($avgRating2>5)
                                                {
                                                    $avgRating2=5;
                                                }

                                                $left2 = 5-$avgRating2;
                                                ?>

                                                @for($i=0;$i<$avgRating2;$i++)
                                                    <li class="active">
                                                        <i class="fa fa-star-o empty tr_all_hover"></i>
                                                        <i class="fa fa-star active tr_all_hover"></i>
                                                    </li>
                                                @endfor
                                                @if($left>0)
                                                    @for($i=0;$i<$left2;$i++)
                                                        <li>
                                                            <i class="fa fa-star-o empty tr_all_hover"></i>
                                                            <i class="fa fa-star active tr_all_hover"></i>
                                                        </li>
                                                    @endfor
                                                @endif

                                            </ul>
                                    </div>
                                    <div class="clearfix position-relative prod-btn" style="bottom: -15px;">
                                    <?php if($relatedProduct->quantity <= 0){ ?>
                                            <!--<button class="button_type_4 bg_scheme_color r_corners tr_all_hover color_light f_left mw_0" >Out of Stock</button>-->
                                            <b style="color: red;">Out of Stock</b>
                                            <?php }else{ ?>
                                                <button class="button_type_4 bg_scheme_color r_corners tr_all_hover color_light f_left mw_0 add-fix item-ad-btn" ng-click="addToCart('productJsonObj_{{$relatedProduct->id}}',1,false)" >Add to Basket</button>
                                            <?php } ?>
                                            <button class="active-wishlist button_type_4 bg_light_color_2 tr_all_hover f_right r_corners color_dark mw_0 m_left_5 p_hr_0 item-com-btn" onclick="compare_product('{{$relatedProduct->id}}');" ><i class="fa fa-files-o"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Compare</span></button>
                                            <div class="abs-loader" id="load-img{{ $relatedProduct->id }}" hidden></div>
                                            @if($relatedProduct->isWished)
                                                <button id="outer{{ $relatedProduct->id }}" class="active-wishlist button_type_4 bg_light_color_2 tr_all_hover f_right r_corners color_dark mw_0 p_hr_0 item-wish-btn" onclick="submitWishListforproductdetails({{$relatedProduct->id}})"><i class="fa fa-heart-o"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span></button>
                                            @else
                                                <button id="outer{{ $relatedProduct->id }}" class="button_type_4 bg_light_color_2 tr_all_hover f_right r_corners color_dark mw_0 p_hr_0 item-wish-btn" onclick="submitWishListforproductdetails({{$relatedProduct->id}})"><i class="fa fa-heart-o"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span></button>
                                            @endif
                                            <div class="notify-small" id="outeraddnotify{{ $relatedProduct->id }}"  hidden><span>Product Added</span></div>
                                            <div class="notify-small" id="outeraddnotify2{{ $relatedProduct->id }}"  hidden><span>Already Added</span></div>
                                    </div>
                                </figcaption>
                            </figure>
                            <div class="popup_wrap d_none" id="quick_view_product_{{$relatedProduct->id}}" >
                                <section class="popup r_corners shadow">
                                    <button class="bg_tr color_dark tr_all_hover text_cs_hover close f_size_large"><i class="fa fa-times"></i></button>
                                    <div class="clearfix">
                                        <div class="custom_scrollbar">
                                            <!--left popup column-->
                                            <div class="f_left half_column">
                                                <div class="relative d_inline_b m_bottom_10 qv_preview">
                                                    {{--<span class="hot_stripe"><img src="{{asset('/template_resource/images/sale_product.png')}}" alt=""></span>--}}
                                                    @foreach(@$relatedProduct->pictures as $picture)
                                                        @if($picture->cover==1)
                                                            <?php $countPic++;?>
                                                            <img id="zoom_image{{$countPic}}" src="{{$imageUrl.'product/large/'.$picture->name}}" class="tr_all_hover" alt="" onerror="this.src='http://placehold.it/90x90'">

                                                        @endif
                                                    @endforeach
                                                </div>
                                                <!--carousel-->

                                                <div class="relative qv_carousel_wrap m_bottom_20">
                                                    <button class="button_type_11 t_align_c f_size_ex_large bg_cs_hover r_corners d_inline_middle bg_tr tr_all_hover qv_btn_prev">
                                                        <i class="fa fa-angle-left "></i>
                                                    </button>
                                                    <ul class="qv_carousel d_inline_middle">
                                                        @foreach($relatedProduct->pictures as $picture)
                                                            <li><img id="small-image{{$picture->name}}" src="{{$imageUrl.'product/thumbnail/'.$picture->name}}" onerror="this.src='http://placehold.it/90x90'" alt="" onclick="changePicture('{{$picture->name}}', '{{ $countPic}}')"></li>
                                                        @endforeach
                                                    </ul>
                                                    <button class="button_type_11 t_align_c f_size_ex_large bg_cs_hover r_corners d_inline_middle bg_tr tr_all_hover qv_btn_next">
                                                        <i class="fa fa-angle-right "></i>
                                                    </button>
                                                </div>

                                                <div class="d_inline_middle">Share this:</div>
                                                <div class="d_inline_middle m_left_5">
                                                    <!-- AddThis Button BEGIN -->
                                                    <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                                        <a class="addthis_button_preferred_1"></a>
                                                        <a class="addthis_button_preferred_2"></a>
                                                        <a class="addthis_button_preferred_3"></a>
                                                        <a class="addthis_button_preferred_4"></a>
                                                        <a class="addthis_button_compact"></a>
                                                        <a class="addthis_counter addthis_bubble_style"></a>
                                                    </div>
                                                    <!-- AddThis Button END -->
                                                </div>
                                            </div>
                                            <!--right popup column-->
                                            <div class="f_right half_column">
                                                <!--description-->
                                                <h2 class="m_bottom_10"><a href="{{url('/product/'.$relatedProduct->id)}}" class="color_dark fw_medium">{{$relatedProduct->title}}</a></h2>
                                                <div class="m_bottom_10">
                                                    <!--rating-->
                                                    <ul class="horizontal_list d_inline_middle type_2 clearfix rating_list tr_all_hover">

                                                        <?php
                                                        $avgRating = ceil($relatedProduct->avgRating);
                                                        if($avgRating>5)
                                                        {
                                                            $avgRating=5;
                                                        }

                                                        $left = 5-$avgRating;
                                                        ?>
                                                        @for($i=0;$i<$avgRating;$i++)
                                                            <li class="active">
                                                                <i class="fa fa-star-o empty tr_all_hover"></i>
                                                                <i class="fa fa-star active tr_all_hover"></i>
                                                            </li>
                                                        @endfor
                                                        @if($left>0)
                                                            @for($i=0;$i<$left;$i++)
                                                                <li>
                                                                    <i class="fa fa-star-o empty tr_all_hover"></i>
                                                                    <i class="fa fa-star active tr_all_hover"></i>
                                                                </li>
                                                            @endfor
                                                        @endif
                                                    </ul>
                                                    <a href="#" class="d_inline_middle default_t_color f_size_small m_left_5"><span id="reviewCount{{$relatedProduct->id}}"></span> Review(s) </a>
                                                </div>
                                                <hr class="m_bottom_10 divider_type_3">
                                                <table class="description_table m_bottom_10">
                                                    <tr>
                                                        <td>Manufacturer:</td>
                                                        <td><a href="#" class="color_dark">{{@$relatedProduct->manufacturer->name}}</a></td>
                                                    </tr>
                                                    <tr>@if(@$relatedProduct->quantity>0)
                                                            <td>Availability:</td>
                                                            <td><span class="color_green">in stock </span>{{@$relatedProduct->quantity}} item(s)</td>
                                                        @else
                                                            <td>Availability:</td>
                                                            <td><span class="colorpicker_rgb_r"><b style="color: red;">Out of stock</b></span>
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <td>Product Code:</td>
                                                        <td>{{@$relatedProduct->code}}</td>
                                                    </tr>
                                                </table>
                                                <h5 class="fw_medium m_bottom_10">Product Dimensions and Weight</h5>
                                                <table class="description_table m_bottom_5">
                                                    <tr>
                                                        <td>Product Length(WxHxD):</td>
                                                        <td><span class="color_dark">{{@$relatedProduct->width}}x{{@$relatedProduct->height}}x{{@$relatedProduct->depth}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Product Weight:</td>
                                                        <td>{{@$relatedProduct->weight}} KG</td>
                                                    </tr>
                                                </table>
                                                <hr class="divider_type_3 m_bottom_10">
                                                <p class="m_bottom_10"><?php print $relatedProduct->description; ?></p>
                                                <hr class="divider_type_3 m_bottom_15">
                                                <div class="m_bottom_15">
                                                    {{--@if($relatedProduct->previousPrice>0)
                                                        <s class="v_align_b f_size_ex_large">&#2547 {{@$relatedProduct->previousPrice}}</s>
                                                    @endif
                                                        <span>&#2547</span> {{@$relatedProduct->prices[0]->retailPrice}}--}}

                                                    @if($relatedProduct->discountActiveFlag)
                                                        <s class="v_align_b f_size_ex_large"><span><?php echo $currency->HTMLCode; ?></span>{{ number_format(($relatedProduct->prices[0]->retailPrice),2)}}</s><span
                                                                class="v_align_b f_size_big m_left_5 scheme_color fw_medium"><span><?php echo $currency->HTMLCode; ?></span>{{ number_format(($relatedProduct->prices[0]->retailPrice-$relatedProduct->discountAmount),2)}}</span>
                                                    @else
                                                        <span
                                                                class="v_align_b f_size_big m_left_5 scheme_color fw_medium"><span><?php echo $currency->HTMLCode; ?></span>{{ number_format($relatedProduct->prices[0]->retailPrice,2)}}</span>

                                                    @endif
                                                </div>

                                                {{--<s class="v_align_b f_size_ex_large"><span>&#2547</span>{{ $package->originalPriceTotal }}</s><span
                                                        class="v_align_b f_size_big m_left_5 scheme_color fw_medium"><span>&#2547</span>{{$package->packagePriceTotal}}</span>--}}

                                                <table class="description_table type_2 m_bottom_15">
                                                    @foreach(@$relatedProduct->attributes as $attributes)
                                                        <tr>
                                                            <td class="v_align_m">{{$attributes->name}}:</td>
                                                            <td class="v_align_m">
                                                                {{--<div class="custom_select f_size_medium relative d_inline_middle">--}}
                                                                {{--<div class="select_title r_corners relative color_dark">Pick</div>--}}
                                                                {{--<ul class="select_list d_none"></ul>--}}


                                                                {{--</div>--}}
                                                                <select name="product_name" id="select_attribute_{{$relatedProduct->id}}_{{$attributes->id}}">
                                                                    @foreach(@$attributes->attributesValue as $attributesValue)
                                                                        <option value="{{@$attributesValue->id}}">{{@$attributesValue->value}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td class="v_align_m">Quantity:</td>
                                                        <td class="v_align_m">
                                                            <div class="clearfix quantity r_corners d_inline_middle f_size_medium color_dark">
                                                                <button class="bg_tr d_block f_left" data-direction="down">-</button>
                                                                <input id="quantity_modifier_{{$relatedProduct->id}}"type="text" name="" readonly value="1" class="f_left">
                                                                <button class="bg_tr d_block f_left" data-direction="up">+</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <input type="hidden" id="productJsonObj_{{$relatedProduct->id}}" value="{{json_encode($relatedProduct)}}"  />
                                                @if(Auth::check())
                                                    <div class="clearfix m_bottom_15 position-relative">
                                                        <button class="button_type_12 r_corners bg_scheme_color color_light tr_delay_hover f_left f_size_large" ng-click="addToCart('productJsonObj_{{$relatedProduct->id}}',-1,true)" >Add to Basket</button>
                                                        <div class="abs-loader" id="load-img2{{ $relatedProduct->id }}" hidden></div>
                                                        @if($relatedProduct->isWished)
                                                            <button id="inner{{ $relatedProduct->id }}" class="active button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0 "  onclick="submitWishListforproductdetails({{$relatedProduct->id}})"><i class="fa fa-heart-o f_size_big "></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span></button>
                                                        @else
                                                            <button id="inner{{ $relatedProduct->id }}" class="button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0 "  onclick="submitWishListforproductdetails({{$relatedProduct->id}})"><i class="fa fa-heart-o f_size_big "></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span></button>

                                                        @endif
                                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0" onclick="compare_product('{{$product->id}}');"><i class="fa fa-files-o f_size_big"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Compare</span></button>
<!--                                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0 relative"><i class="fa fa-question-circle f_size_big"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Ask a Question</span></button>-->
                                                        <div class="notify-small" id="outeraddnotify{{ $relatedProduct->id }}"  hidden><span>Product Added</span></div>
                                                        <div class="notify-small" id="outeraddnotify2{{ $relatedProduct->id }}"  hidden><span>Already Added</span></div>
                                                    </div>
                                                @else
                                                    <div class="clearfix m_bottom_15 position-relative">
                                                        <button class="button_type_12 r_corners bg_scheme_color color_light tr_delay_hover f_left f_size_large" ng-click="addToCart('productJsonObj_{{$relatedProduct->id}}',-1,true)" >Add to Basket</button>
                                                        <div class="abs-loader" id="load-img2{{ $relatedProduct->id }}" hidden></div>
                                                        <button id="inner{{ $relatedProduct->id }}" class="button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0 "  onclick="submitWishListforproductdetails('{{$relatedProduct->id}}','quick_view_product_{{$relatedProduct->id}}')"><i class="fa fa-heart-o f_size_big "></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Wishlist</span></button>
                                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0" onclick="compare_product('{{$product->id}}');"><i class="fa fa-files-o f_size_big"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Compare</span></button>
<!--                                                        <button class="button_type_12 bg_light_color_2 tr_delay_hover f_left r_corners color_dark m_left_5 p_hr_0 relative"><i class="fa fa-question-circle f_size_big"></i><span class="tooltip tr_all_hover r_corners color_dark f_size_small">Ask a Question</span></button>-->
                                                        <div class="notify-small" id="loginnotify{{ $relatedProduct->id }}"  hidden><span>Please Login First</span></div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        @endforeach
                    </div>
                    <hr class="divider_type_3 m_bottom_15">
                    {{--<a href="category_grid.html" role="button"
                       class="d_inline_b bg_light_color_2 color_dark tr_all_hover button_type_4 r_corners"><i
                                class="fa fa-reply m_left_5 m_right_10 f_size_large"></i>Back to: Woman</a>--}}
                                
                    <?php } ?>
                </section>
                <!--right column-->
                <aside class="col-lg-3 col-md-3 col-sm-3">
                    
                    
                    <!--banner-->
                    
                    <!--Recent Products-->
                    @include('web/partial/sideblock/recentview')
                    <!--Bestsellers-->
                    @include('web/partial/sideblock/bestsellers')
                    <a href="#" class="d_block r_corners m_bottom_30 hidden-xs">
                        <img src="{{asset('/template_resource/images/banner_img_6.jpg')}}" alt="">
                    </a>
                    <!--tags-->
                    @include('web/partial/sideblock/tags')
                </aside>
            </div>
        </div>
    </div>
    <!--markup footer-->
    @include('web/partial/footer/newbottom')
</div>
<!--social widgets-->
@include('web/partial/social_widgets/main')
<ul class="social_widgets d_xs_none">
    facebook
    <li class="relative">
        <button class="sw_button t_align_c facebook"><i class="fa fa-facebook"></i></button>
        <div class="sw_content">
            <h3 class="color_dark m_bottom_20">Join Us on Facebook</h3>
            <iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fenvato&amp;width=235&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false&amp;appId=438889712801266"
                    style="border:none; overflow:hidden; width:235px; height:258px;"></iframe>
        </div>
    </li>
    twitter feed
    <li class="relative">
        <button class="sw_button t_align_c twitter"><i class="fa fa-twitter"></i></button>
        <div class="sw_content">
            <h3 class="color_dark m_bottom_20">Latest Tweets</h3>

            <div class="twitterfeed m_bottom_25"></div>
            <a role="button" class="button_type_4 d_inline_b r_corners tr_all_hover color_light tw_color"
               href="https://twitter.com/fanfbmltemplate">Follow on Twitter</a>
        </div>
    </li>
    contact form
    <li class="relative">
        <button class="sw_button t_align_c contact"><i class="fa fa-envelope-o"></i></button>
        <div class="sw_content">
            <h3 class="color_dark m_bottom_20">Contact Us</h3>

            <p class="f_size_medium m_bottom_15">Lorem ipsum dolor sit amet, consectetuer adipis mauris</p>

            <form id="contactform" class="mini">
                <input class="f_size_medium m_bottom_10 r_corners full_width" type="text" name="cf_name"
                       placeholder="Your name">
                <input class="f_size_medium m_bottom_10 r_corners full_width" type="email" name="cf_email"
                       placeholder="Email">
                <textarea class="f_size_medium r_corners full_width m_bottom_20" placeholder="Message"
                          name="cf_message"></textarea>
                <button type="submit" class="button_type_4 r_corners mw_0 tr_all_hover color_dark bg_light_color_2">
                    Send
                </button>
            </form>
        </div>
    </li>
    contact info
    <li class="relative">
        <button class="sw_button t_align_c googlemap"><i class="fa fa-map-marker"></i></button>
        <div class="sw_content">
            <h3 class="color_dark m_bottom_20">Store Location</h3>
            <ul class="c_info_list">
                <li class="m_bottom_10">
                    <div class="clearfix m_bottom_15">
                        <i class="fa fa-map-marker f_left color_dark"></i>

                        <p class="contact_e">8901 Marmora Road,<br> Glasgow, D04 89GR.</p>
                    </div>
                    <iframe class="r_corners full_width" id="gmap_mini"
                            src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=ru&amp;geocode=&amp;q=Manhattan,+New+York,+NY,+United+States&amp;aq=0&amp;oq=monheten&amp;sll=37.0625,-95.677068&amp;sspn=65.430355,129.814453&amp;t=m&amp;ie=UTF8&amp;hq=&amp;hnear=%D0%9C%D0%B0%D0%BD%D1%85%D1%8D%D1%82%D1%82%D0%B5%D0%BD,+%D0%9D%D1%8C%D1%8E-%D0%99%D0%BE%D1%80%D0%BA,+%D0%9D%D1%8C%D1%8E+%D0%99%D0%BE%D1%80%D0%BA,+%D0%9D%D1%8C%D1%8E-%D0%99%D0%BE%D1%80%D0%BA&amp;ll=40.790278,-73.959722&amp;spn=0.015612,0.031693&amp;z=13&amp;output=embed"></iframe>
                </li>
                <li class="m_bottom_10">
                    <div class="clearfix m_bottom_10">
                        <i class="fa fa-phone f_left color_dark"></i>

                        <p class="contact_e">800-559-65-80</p>
                    </div>
                </li>
                <li class="m_bottom_10">
                    <div class="clearfix m_bottom_10">
                        <i class="fa fa-envelope f_left color_dark"></i>
                        <a class="contact_e default_t_color" href="mailto:#">info@companyname.com</a>
                    </div>
                </li>
                <li>
                    <div class="clearfix">
                        <i class="fa fa-clock-o f_left color_dark"></i>

                        <p class="contact_e">Monday - Friday: 08.00-20.00 <br>Saturday: 09.00-15.00<br> Sunday: closed
                        </p>
                    </div>
                </li>
            </ul>
        </div>
    </li>
</ul>
<!--custom popup-->

<!--login popup-->

<button class="t_align_c r_corners tr_all_hover type_2 animate_ftl" id="go_to_top"><i class="fa fa-angle-up"></i>
</button>

<!--scripts include-->
@include('web/partial/script/core')

<script src="{{asset('/template_resource/js/scripts.js')}}"></script>
<script src="{{asset('/template_resource/elevatezoom/jquery.elevatezoom.js')}}"></script>
<script src="{{asset('/template_resource/elevatezoom/jquery.mousewheel-3.0.6.pack.js')}}"></script>
<script src="{{asset('/template_resource/elevatezoom/jquery.fancybox.js')}}"></script>


<input type="hidden" id="currentProductId" value="{{$product->id}}" />
<script>
 //initiate the plugin and pass the id of the div containing gallery images
$("#product").elevateZoom({gallery:'productgallery', cursor: 'pointer', galleryActiveClass: 'active', imageCrossfade: true, loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'}); 

//pass the images to Fancybox
$("#product").bind("click", function(e) {  
  var ez =   $('#product').data('elevateZoom');	
	$.fancybox(ez.getGalleryList());
  return false;
});
</script>
<script type="text/javascript">

jQuery(document).ready(function($){

	$('#image1').addimagezoom({ // single image zoom
		zoomrange: [3, 10],
		magnifiersize: [300,300],
		magnifierpos: 'right',
		cursorshade: true,
		largeimage: 'hayden.jpg' //<-- No comma after last option!
	})


	$('#image2').addimagezoom() // single image zoom with default options
	
	$('#zoom_image').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
//		descArea: '#description', // description selector (optional - but required if descriptions are used) - new
		speed: 1500, // duration of fade in for new zoomable images (in milliseconds, optional) - new
//		descpos: false, // if set to true - description position follows image position at a set distance, defaults to false (optional) - new
		imagevertcenter: true, // zoomable image centers vertically in its container (optional) - new
		magvertcenter: true, // magnified area centers vertically in relation to the zoomable image (optional) - new
		zoomrange: [3, 10],
		magnifiersize: [450,450],
		magnifierpos: 'right',
		cursorshadecolor: '#fdffd5',
		cursorshade: true //<-- No comma after last option!
	});
	
	$('#multizoom2').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
		descArea: '#description2', // description selector (optional - but required if descriptions are used) - new
		disablewheel: true // even without variable zoom, mousewheel will not shift image position while mouse is over image (optional) - new
				//^-- No comma after last option!	
	});
	
})

</script>

<script>
    function submitWishListforproductdetails(productId,quickViewId){

        jQuery('#load-img'+productId).show();
        jQuery('#load-img2'+productId).show();
        jQuery.ajax({
            url: jQuery("#baseUrl").val()+"api/customer/wishlist/add",
            method: "POST",
            data: {
                "product_id" : productId

            },
            success: function (data) {
                if(!data.responseStat.isLogin)
                {
                    showLoginForm("");
                    jQuery('#load-img'+productId).hide();
                    jQuery('#load-img2'+productId).hide();
                    

                    console.log(data);
                }
                else{
                   $('.favourite_'+productId).removeClass('fa-heart-o');
                    $('.favourite_'+productId).addClass('fa-heart');
                    $('#load-img'+productId).hide();
                    $('#load-img2'+productId).hide();
                    $('#inner'+productId).addClass("active");
                    $('#outer'+productId).addClass("active");

                    console.log(data);
                }

                if(data.responseStat.status){
                    try{
                        increaseDecreaseWishLisCount(1);
                        jQuery('#addnotify'+productId).show();
                        jQuery('#addnotify'+productId).fadeOut(2000);
                        jQuery('#outeraddnotify'+productId).show();
                        jQuery('#outeraddnotify'+productId).fadeOut(2000);
                    }catch(ex){
                        console.log(ex);
                    }

                }else{
                    if(!data.responseStat.isLogin){
                        return;
                    }

                    jQuery('#loginnotify'+productId).show();
                    jQuery('#loginnotify'+productId).fadeOut(2000);
                    jQuery('#addnotify2'+productId).show();
                    jQuery('#addnotify2'+productId).fadeOut(2000);
                    jQuery('#outeraddnotify2'+productId).show();
                    jQuery('#outeraddnotify2'+productId).fadeOut(2000);
                }
            }

        });

    }
    function getReviewPoint(){
       return jQuery('#reviewPoint').find("li.active").length;
    }

    function submitReview(productId)
    {
        jQuery("#notification").html("processing...");

        jQuery.ajax({

            url: jQuery("#baseUrl").val() + "api/customer/review/add",
            method: "POST",
            data: {
                "product_id": productId,
                "note":jQuery('#note').val(),
                "rating": getReviewPoint()
            },
            success: function (data) {
                if (data.responseStat.status) {
                    jQuery("#notification").html(data.responseStat.msg);
                    jQuery("#notification").show();
                    jQuery("#notification").fadeOut(3000);
                    jQuery('#note').val("");
                    jQuery('#reviewLoader').show();
                    jQuery.ajax({

                        url: jQuery("#baseUrl").val() + "review/get"+
                        "?product_id="+jQuery('#productId').html(),
                        method: "POST",
                        data: {
                        },
                        success: function (data) {

                            jQuery('#reviewLoader').hide();
                            jQuery('#reviews').html(data);
                            
                            
                            jQuery('#offset').html(1);
                            jQuery('#reviewCount'+"<?php echo $product->id; ?>").html(parseInt(jQuery('#reviewCount'+"<?php echo $product->id; ?>").html())+1);
                            if(parseInt(jQuery('#reviewCount'+"<?php echo $product->id; ?>").html()) > jQuery("#reviews > article").length){
                                jQuery('#loaderbtn').fadeIn(1000);
                            }

                        }

                    });

//                    jQuery('#reviewLoader').hide();
//                    console.log(data.htmlData);
//                    jQuery('#reviews').prepend(data.htmlData);
                    console.log(data);

                }
                else {
                    //jQuery("#errmsg").html(data.responseStat.msg);
                    jQuery("#notification").html(data.responseStat.msg);
                    jQuery("#notification").show();
                    jQuery("#notification").fadeOut(3000,function(){
                        if(!data.responseStat.isLogin) {
                            showLoginForm("");
                        }
                    });
                    console.log(data);
                }

            }

        });
    }

    jQuery(document).ready(function() {
        jQuery('#reviewLoader').show();
        jQuery.ajax({
            url: jQuery("#baseUrl").val() + "review/get",
            method: "POST",
            data: {
                "product_id": jQuery('#productId').html(),
            },
            success: function (data) {

                jQuery('#reviewLoader').hide();
                jQuery('#reviews').html(data);
            }

        });
    });

    function changePicture(picName)
    {
        srcOfZoom = jQuery('#zoom_image').attr('src');
        parts = srcOfZoom.split("/");

        newSource = (parts[0]+"/"+parts[1]+"/"+parts[2]+"/"+parts[3]+"/"+parts[4]+"/"+parts[5]+"/"+parts[6]+"/"+parts[7]+"/"+picName);

        jQuery("#zoom_image").attr("src",newSource);

    }

    jQuery(document).ready(function() {

        jQuery.ajax({

            url: jQuery("#baseUrl").val() + "api/review/count",
            method: "POST",
            data: {
                "product_id": "<?php echo $product->id; ?>"
            },
            success: function (data) {
                jQuery('#reviewCount'+"<?php echo $product->id; ?>").html(data.responseData);
                if(parseInt(data.responseData)<=3)
                {
                    jQuery('#loaderbtn').hide();
                }
                console.log("count"+data.responseData +"<?php echo "pro id :".$product->id; ?>");

            }

        });

        expandCategoryTree();


    });
    function expandCategoryTree(){
        var productId = jQuery('#currentProductId').val();
        var product = JSON.parse(jQuery('#productJsonObj_'+productId).val());
        var categoryId = 0;
        try{
            categoryId = product.categories[0].id;
        }catch(ex){
            console.log(ex);
        }
        /* Tamil Code Started [ Need Dynamic approach to solve this problem, can you do it!!! ]*/
        jQuery('#categorySideTree_'+categoryId).parent().children('li>a').parent().parent().parent().children('li>a').parent().parent().parent().children('li>a').click();
        jQuery('#categorySideTree_'+categoryId).parent().children('li>a').parent().parent().parent().children('li>a').click();
        jQuery('#categorySideTree_'+categoryId).parent().children('li>a').click();
        /* Tamil Code Ends */

    }

    function loadMoreReviews(){
//        console.log('total count: '+jQuery('#reviewCount'+"<?php // echo jQueryproduct->id; ?>").html());
//        console.log('articles: '+jQuery("#reviews > article").length);
        jQuery('#reviewLoader').show();

        jQuery.ajax({
            url: jQuery("#baseUrl").val() + "review/get",
            method: "POST",
            data: {
                "offset" : jQuery('#offset').html(),
                "product_id": "<?php echo $product->id; ?>"
            },
            success: function (data) {
                if(data != ""){
                    jQuery('#offset').html(parseInt(jQuery('#offset').html())+1);
                    jQuery('#reviews').append(data);
                    jQuery('#reviewLoader').hide();
                }
//                else{
//                    jQuery('#reviewLoader').hide();
//                    jQuery('#loaderbtn').fadeOut(2000);
//                }
                jQuery('#reviewLoader').hide();
                if(parseInt(jQuery('#reviewCount'+"<?php echo $product->id; ?>").html()) == jQuery("#reviews > article").length){
                    jQuery('#loaderbtn').fadeOut(2000);
                }
            }

        });
    }

    function reviewandcomment(tab_id){
        $('#tab-load-'+tab_id).click();
        $('html, body').animate({
            scrollTop: $("#scroll_div").offset().top
        }, 1000);
    }


</script>
</body>
</html>