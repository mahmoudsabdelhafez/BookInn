
@extends('frontend.main_master')
@section('main')

    <!-- Inner Banner -->
    <div class="inner-banner inner-bg4">
        <div class="container">
            <div class="inner-title">
                <ul>
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>Blog</li>
                </ul>
                <h3>Blog</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Blog Style Area -->
    <div class="blog-style-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    
                    @foreach ($blog as $item ) 
                   
                    <div class="col-lg-12">
                        <div class="blog-card">
                            <div class="row align-items-center">
                                <div class="col-lg-5 col-md-4 p-0">
                                    <div class="blog-img">
                                        <a href="{{ url('blog/details/'.$item->post_slug) }}">
                                            <img src="{{ asset($item->post_image) }}" alt="Images">
                                        </a>
                                    </div>
                                </div>

                <div class="col-lg-7 col-md-8 p-0">
                    <div class="blog-content">
                <span>{{ $item->created_at->format('M d Y')  }}</span>
                        <h3>
                            <a href="{{ url('blog/details/'.$item->post_slug) }}">{{ $item->post_title }}</a>
                        </h3>
                        <p>{{ $item->short_descp }}</p>
                        <a href="{{ url('blog/details/'.$item->post_slug) }}" class="read-btn">
                            Read More
                        </a>
                    </div>
                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                

                    <div class="col-lg-12 col-md-12">
                        <div class="pagination-area">
                            {{-- Link with vendor/pagination/custom.blade file to enable pagination --}}
                            {{ $blog->links('vendor.pagination.custom') }}   
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="side-bar-wrap">
                        
                        <div class="services-bar-widget">
                            <h3 class="title">Blog Category</h3>
                            <div class="side-bar-categories">
                                @foreach ($bcategory as $cat) 
                                <ul>
                                    <li>
                                        <a href="{{ url('blog/cat/list/'.$cat->id) }}">{{ $cat->category_name }}</a>
                                    </li> 
                                </ul>
                                @endforeach
                            </div>
                        </div>
                        <div class="side-bar-widget">
                            <h3 class="title">Recent Posts</h3>
                            <div class="widget-popular-post">
                                @foreach ($lpost as $post)   
                            <article class="item">
                                <a href="{{ url('blog/details/'.$post->post_slug)}}" class="thumb">
                <img src="{{ asset($post->post_image) }}" alt="Images" style="width: 80px; height:80px;">      
                                </a>
                                <div class="info">
                                    <h4 class="title-text">
                                        <a href="{{ url('blog/details/' . $post->post_slug) }}">
                                            {{ $post->post_title }}
                                        </a>
                                    </h4>
                                    
                                </div>
                            </article>
                            @endforeach

                                
                            </div>
                        </div>

                     
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog Style Area End -->





@endsection