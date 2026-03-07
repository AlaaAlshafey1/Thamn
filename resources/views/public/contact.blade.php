@extends('layouts.public')

@section('title', $title)

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gold: #c1953e;
            --dark-gold: #a07a2d;
            --light-gold: #e5c07b;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --text-dark: #2d3436;
            --text-muted: #636e72;
        }

        .contact-container {
            position: relative;
            padding: 40px 20px;
            overflow: hidden;
        }

        .hero-section {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            padding: 60px 0;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .hero-section h1 {
            font-weight: 800;
            color: var(--text-dark);
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(to right, var(--primary-gold), var(--dark-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-section p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Info Cards */
        .info-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(193, 149, 62, 0.1);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: rgba(193, 149, 62, 0.1);
            color: var(--primary-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }

        .info-card:hover .icon-box {
            background: var(--primary-gold);
            color: #fff;
        }

        .info-card h4 {
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .info-card p {
            color: var(--text-muted);
            margin-bottom: 0;
            dir: ltr;
            /* To keep phone numbers properly formatted if they contain numbers */
        }

        /* Glassmorphism Form */
        .form-wrapper {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            margin-top: 40px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #eee;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.2rem rgba(193, 149, 62, 0.1);
            background: #fff;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%);
            border: none;
            color: #fff;
            padding: 12px 35px;
            border-radius: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(193, 149, 62, 0.2);
        }

        .submit-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 30px rgba(193, 149, 62, 0.3);
        }

        .social-links a {
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: #f1f3f5;
            color: var(--text-dark);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background: var(--primary-gold);
            color: #fff;
            transform: rotate(360deg);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-up {
            animation: fadeInUp 0.8s ease backwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.2s;
        }

        .delay-3 {
            animation-delay: 0.3s;
        }

        .delay-4 {
            animation-delay: 0.4s;
        }

        /* RTL specific adjustments */
        [dir="rtl"] .info-card p {
            direction: ltr;
            display: inline-block;
        }
    </style>

    <div class="contact-container">
        <div class="hero-section animate-up">
            <h1>{{ $title }}</h1>
            <p>{{ lang('يسعدنا تواصلكم معنا، نحن هنا للإجابة على جميع استفساراتكم.', 'We are happy to hear from you, we are here to answer all your inquiries.', request()) }}
            </p>
        </div>

        <div class="row g-4">
            <!-- Phone Info -->
            <div class="col-md-4 animate-up delay-1">
                <div class="info-card">
                    <div class="icon-box">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4>{{ lang('اتصل بنا', 'Call Us', request()) }}</h4>
                    <p>{{ $contactInfo->phone ?? '+966 000 000 000' }}</p>
                </div>
            </div>

            <!-- Email Info -->
            <div class="col-md-4 animate-up delay-1">
                <div class="info-card">
                    <div class="icon-box">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>{{ lang('البريد الإلكتروني', 'Email Us', request()) }}</h4>
                    <p>{{ $contactInfo->email ?? 'info@thamn.com' }}</p>
                </div>
            </div>

            <!-- Social Media Info -->
            <div class="col-md-4 animate-up delay-1">
                <div class="info-card">
                    <div class="icon-box">
                        <i class="fas fa-share-nodes"></i>
                    </div>
                    <h4>{{ lang('تابعنا', 'Follow Us', request()) }}</h4>
                    <div class="social-links">
                        @forelse($socialMedia as $social)
                            <a href="{{ $social['url'] }}" title="{{ $social['name'] }}" target="_blank">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @empty
                            <!-- This case is handled by controller providing defaults -->
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-wrapper animate-up delay-2">
                    <h3 class="text-center mb-4" style="font-weight: 700;">
                        {{ lang('أرسل لنا رسالة', 'Send Us a Message', request()) }}
                    </h3>

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4 animate-up">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('public.contact.submit') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ lang('الاسم', 'Name', request()) }}</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="{{ lang('أدخل اسمك', 'Enter your name', request()) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ lang('البريد الإلكتروني', 'Email', request()) }}</label>
                                <input type="email" name="email" class="form-control"
                                    placeholder="{{ lang('أدخل بريدك الإلكتروني', 'Enter your email', request()) }}"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ lang('الموضوع', 'Subject', request()) }}</label>
                            <input type="text" name="subject" class="form-control"
                                placeholder="{{ lang('موضوع الرسالة', 'Subject of message', request()) }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ lang('الرسالة', 'Message', request()) }}</label>
                            <textarea name="message" class="form-control" rows="5"
                                placeholder="{{ lang('اكتب رسالتك هنا...', 'Write your message here...', request()) }}"
                                required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane me-2"></i> {{ lang('إرسال الآن', 'Send Now', request()) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection