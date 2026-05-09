<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ثمن - Thamn') }}</title>

        <!-- Fonts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Cairo', sans-serif;
            }
            .glass {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .hero-gradient {
                background: linear-gradient(180deg, rgba(28, 28, 30, 0.4) 0%, rgba(28, 28, 30, 0.95) 100%);
            }
        </style>
    </head>
    <body class="bg-light-bg text-dark-bg antialiased">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="main-header">
            <div class="container mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex flex-col items-center justify-center gap-1">
                        <div class="flex gap-[2px] items-end h-4">
                            <div class="w-[2px] h-3 bg-white"></div>
                            <div class="w-[4px] h-4 bg-white"></div>
                            <div class="w-[2px] h-2 bg-white"></div>
                            <div class="w-[3px] h-4 bg-white"></div>
                            <div class="w-[2px] h-3 bg-white"></div>
                            <div class="w-[5px] h-4 bg-white"></div>
                            <div class="w-[2px] h-2 bg-white"></div>
                        </div>
                        <div class="w-full h-[2px] bg-white"></div>
                        <div class="text-[10px] font-black tracking-widest text-white leading-none">ثمن</div>
                    </div>
                    <span class="text-white text-2xl font-black tracking-tight" style="font-family: 'Cairo';">ثمن</span>
                </div>
                <nav class="hidden md:flex items-center gap-8 text-white/90">
                    <a href="#" class="hover:text-primary transition-colors">الرئيسية</a>
                    <a href="#" class="hover:text-primary transition-colors">خدماتنا</a>
                    <a href="#" class="hover:text-primary transition-colors">عن ثمن</a>
                    <a href="#" class="hover:text-primary transition-colors">اتصل بنا</a>
                </nav>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2 rounded-full bg-primary text-white font-bold hover:bg-orange-500 transition-all shadow-lg shadow-primary/20">لوحة التحكم</a>
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-primary transition-colors">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="px-6 py-2 rounded-full bg-primary text-white font-bold hover:bg-orange-500 transition-all shadow-lg shadow-primary/20">ابدأ الآن</a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative h-screen flex items-center justify-center overflow-hidden bg-dark-bg">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1512428559087-560fa5ceab42?auto=format&fit=crop&q=80&w=2000" alt="Hero Background" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 hero-gradient"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10 text-center text-white mt-20">
                <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight">
                    خلينا نبدأ <span class="text-primary">تثمين منتجك</span>
                </h1>
                <p class="text-lg md:text-xl text-white/70 max-w-2xl mx-auto mb-10 leading-relaxed">
                    جاوب على شوية أسئلة وخذ تقييم دقيق لمنتجك من بوابة رقمية احترافية.
                </p>
                
                <div class="max-w-xl mx-auto glass p-2 rounded-2xl flex items-center gap-2">
                    <div class="flex-1 text-right px-4 py-3 text-white/50">
                        اختر مجال التثمين <span class="text-primary font-bold">سيارات</span>
                    </div>
                    <button class="bg-primary hover:bg-orange-500 text-white w-12 h-12 rounded-xl flex items-center justify-center transition-all shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <div class="mt-12 flex items-center justify-center gap-6">
                    <div class="flex items-center gap-2 bg-dark-bg/50 px-4 py-2 rounded-full border border-white/10">
                        <span class="text-primary">●</span>
                        <span class="text-sm">راح ناخذ ( 5 د ) تقريبًا من وقتك</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="py-24 bg-light-bg">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 text-dark-bg">تقدّر تحفظ وتكمل بياناتك لاحقًا</h2>
                    <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Step 1 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 hover:-translate-y-2 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-dark-bg text-white rounded-full flex items-center justify-center font-bold text-xl group-hover:bg-primary transition-colors">1</div>
                            <div class="text-primary opacity-20 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-dark-bg">معلومات المنتج الأساسية</h3>
                        <p class="text-gray-500 leading-relaxed">
                            اسمه، نوعه، موديله، ومواصفاته العامة التي تميزه.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 hover:-translate-y-2 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-dark-bg text-white rounded-full flex items-center justify-center font-bold text-xl group-hover:bg-primary transition-colors">2</div>
                            <div class="text-primary opacity-20 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-dark-bg">حالته واستخدامه</h3>
                        <p class="text-gray-500 leading-relaxed">
                            من استخدمه؟ قد تصلّح؟ خفيف ولا ثقيل عليه الشغل؟
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 hover:-translate-y-2 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-dark-bg text-white rounded-full flex items-center justify-center font-bold text-xl group-hover:bg-primary transition-colors">3</div>
                            <div class="text-primary opacity-20 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-dark-bg">التقييم العادل</h3>
                        <p class="text-gray-500 leading-relaxed">
                            احصل على سعر تقديري دقيق بناءً على معايير السوق الحالية.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20">
            <div class="container mx-auto px-6">
                <div class="bg-dark-bg rounded-[3rem] p-12 md:p-20 relative overflow-hidden text-center text-white">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[100px] rounded-full"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-900/20 blur-[100px] rounded-full"></div>
                    
                    <h2 class="text-3xl md:text-5xl font-black mb-8 relative z-10">جاهز لتقييم منتجك؟</h2>
                    <p class="text-white/60 mb-12 max-w-xl mx-auto text-lg relative z-10">انضم إلى آلاف المستخدمين الذين يثقون في ثمن للحصول على تقييمات عادلة ودقيقة.</p>
                    <a href="{{ route('register') }}" class="inline-block px-12 py-5 rounded-full bg-primary text-white font-black text-xl hover:bg-orange-500 transition-all shadow-2xl shadow-primary/40 relative z-10">سجل الآن مجاناً</a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-50 py-12 border-t border-gray-100">
            <div class="container mx-auto px-6 text-center">
                <img src="{{ asset('assets/img/Logo.png') }}" alt="Thamn Logo" class="h-12 mx-auto mb-6 opacity-30 grayscale">
                <p class="text-gray-400">&copy; {{ date('Y') }} ثمن. جميع الحقوق محفوظة.</p>
            </div>
        </footer >

        <script>
            // Header scroll effect
            window.addEventListener('scroll', function() {
                const header = document.getElementById('main-header');
                const logo = document.getElementById('logo-img');
                if (window.scrollY > 50) {
                    header.classList.add('bg-white', 'shadow-md');
                    header.classList.remove('py-4');
                    header.classList.add('py-2');
                    header.querySelectorAll('nav a, div a').forEach(a => {
                        if(!a.classList.contains('bg-primary')) a.classList.replace('text-white', 'text-dark-bg');
                    });
                    logo.classList.remove('brightness-0', 'invert');
                } else {
                    header.classList.remove('bg-white', 'shadow-md');
                    header.classList.remove('py-2');
                    header.classList.add('py-4');
                    header.querySelectorAll('nav a, div a').forEach(a => {
                        if(!a.classList.contains('bg-primary')) a.classList.replace('text-dark-bg', 'text-white');
                    });
                    logo.classList.add('brightness-0', 'invert');
                }
            });
        </script>
    </body>
</html>
