@php
    $setting = App\Models\SiteSetting::find(1);
@endphp

<footer class="footer-area footer-bg">
    <div class="container">
        <div class="footer-top pt-100 pb-70">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a href="index.html">
                                <img src="{{ asset($setting->logo) }}" alt="Images">
                            </a>
                        </div>
                        <p>
                            BookInn offers a seamless booking experience, combining elegance with convenience. Discover your perfect stay with just a few clicks, and let comfort meet luxury. </p>
                        <ul class="footer-list-contact">
                            <li>
                                <i class='bx bx-home-alt'></i>
                                <a href="#">{{ $setting->address }}</a>
                            </li>
                            <li>
                                <i class='bx bx-phone-call'></i>
                                <a href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a>
                            </li>
                            <li>
                                <i class='bx bx-envelope'></i>
                                <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget pl-5">
                        <h3>Links</h3>
                        <ul class="footer-list">
                            <li>
                                <a href="{{route('contact.us')}}" >
                                    <i class='bx bx-caret-right'></i>
                                    About Us
                                </a>
                            </li> 
                            <li>
                                <a href="#services" >
                                    <i class='bx bx-caret-right'></i>
                                    Services
                                </a>
                            </li> 
                            <li>
                                <a href="#team">
                                    <i class='bx bx-caret-right'></i>
                                    Team
                                </a>
                            </li> 
                            <li>
                                <a href="{{route('show.gallery')}}" >
                                    <i class='bx bx-caret-right'></i>
                                    Gallery
                                </a>
                            </li> 
                            <li>
                                <a href="terms-condition.html" target="_blank">
                                    <i class='bx bx-caret-right'></i>
                                    Terms 
                                </a>
                            </li> 
                            <li>
                                <a href="privacy-policy.html" target="_blank">
                                    <i class='bx bx-caret-right'></i>
                                    Privacy Policy
                                </a>
                            </li> 
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h3> Links</h3>
                        <ul class="footer-list">
                            <li>
                                <a href="{{route('home')}}" >
                                    <i class='bx bx-caret-right'></i>
                                    Home
                                </a>
                            </li> 
                            <li>
                                <a href="#blog" >
                                    <i class='bx bx-caret-right'></i>
                                    Blog
                                </a>
                            </li> 
                            <li>
                                <a href="#faq" >
                                    <i class='bx bx-caret-right'></i>
                                    FAQ
                                </a>
                            </li> 
                            <li>
                                <a href="#testimonials" >
                                    <i class='bx bx-caret-right'></i>
                                    Testimonials
                                </a>
                            </li> 
                            <li>
                                <a href="{{route('froom.all')}}" >
                                    <i class='bx bx-caret-right'></i>
                                    Rooms
                                </a>
                            </li> 
                            <li>
                                <a href="contact.html" target="_blank">
                                    <i class='bx bx-caret-right'></i>
                                    Contact Us
                                </a>
                            </li> 
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h3>Newsletter</h3>
                        <p>
                            Stay Updated with BookInn!
Join our newsletter to receive the latest offers, news, and updates from BookInn.  Sign up now and start planning your perfect getaway with us!


                        </p>
                        <div class="footer-form">
                            <form class="newsletter-form" data-toggle="validator" method="POST">
                                <div class="row">
                                   

                                    <div class="col-lg-12 col-md-12">
                                        <button type="submit" class="default-btn btn-bg-one">
                                            <a style="color: #fff" href="{{ route('froom.all') }}">Book Now</a>
                                        </button>
                                        <div id="validator-newsletter" class="form-result"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="copy-right-area">
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="copy-right-text text-align1">
                        <p>
                            {{ $setting->copyright }}
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="social-icon text-align2">
                        <ul class="social-link">
                            <li>
                                <a href="{{ $setting->facebook }}" target="_blank"><i class='bx bxl-facebook'></i></a>
                            </li> 
                            <li>
                                <a href="{{ $setting->twitter }}" target="_blank"><i class='bx bxl-twitter'></i></a>
                            </li> 
                            <li>
                                <a href="#" target="_blank"><i class='bx bxl-instagram'></i></a>
                            </li> 
                            <li>
                                <a href="#" target="_blank"><i class='bx bxl-pinterest-alt'></i></a>
                            </li> 
                            <li>
                                <a href="#" target="_blank"><i class='bx bxl-youtube'></i></a>
                            </li> 
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>