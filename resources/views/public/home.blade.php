<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ثمن — التقييم الذكي للمقتنيات الفاخرة</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
--bg:#FFFFFF;
--bg-alt:#F9F9FB;
--primary:#1A1A1A;
--secondary:#555555;
--gold:#C1953E;
--gold-light:#D4AF37;
--gold-soft:rgba(193, 149, 62, 0.1);
--text-main:#1A1A1A;
--text-muted:#666666;
--border:rgba(0,0,0,0.06);
--shadow-sm:0 2px 4px rgba(0,0,0,0.05);
--shadow-md:0 10px 30px rgba(0,0,0,0.08);
--shadow-lg:0 20px 50px rgba(0,0,0,0.12);
}
html{scroll-behavior:smooth}
body{
  background:var(--bg);
  color:var(--text-main);
  font-family:'Cairo',sans-serif;
  line-height:1.6;
  overflow-x:hidden;
}

/* Custom Selection */
::selection { background: var(--gold); color: white; }

/* NAV */
nav{
  position:fixed;top:0;left:0;right:0;z-index:1000;
  padding:1rem 5%;
  display:flex;align-items:center;justify-content:space-between;
  background:rgba(255,255,255,0.85);
  backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);
  transition: all 0.4s ease;
}
nav.scrolled { padding: 0.7rem 5%; box-shadow: var(--shadow-md); }

.logo{ display:flex;align-items:center;gap:0.8rem; text-decoration: none; color: inherit; }
.logo img { height: 45px; transition: transform 0.3s; }
.logo:hover img { transform: scale(1.05); }
.logo span{color:var(--primary); font-family: 'Amiri', serif; font-size: 1.8rem; font-weight: 700;}

nav ul{list-style:none;display:flex;gap:2.5rem}
nav ul a{
  font-size:0.95rem;font-weight:600;
  color:var(--secondary);text-decoration:none;
  transition:all 0.3s;
  position: relative;
}
nav ul a:hover{color:var(--gold)}

.nav-cta{
  padding:0.75rem 1.8rem;
  background: var(--primary);
  color: #FFF;
  font-size:0.9rem; font-weight: 700;
  border-radius:100px;
  transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  display: inline-block;
}
.nav-cta:hover{ background: var(--gold); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(193, 149, 62, 0.3); }

/* HERO */
#hero{
  min-height:100vh;
  display:flex;align-items:center;
  padding:120px 5% 60px;
  position:relative;
  background: radial-gradient(circle at 80% 20%, rgba(193, 149, 62, 0.03) 0%, transparent 40%);
}
.hero-content{
  display:grid;grid-template-columns:1fr 1fr;
  gap:4rem;align-items:center;
  max-width:1400px;margin:0 auto;width:100%;
}

.hero-tag {
    display: inline-flex; align-items:center; gap:0.6rem;
    padding: 0.5rem 1.2rem;
    background: var(--gold-soft);
    color: var(--gold);
    border-radius: 100px;
    font-size: 0.9rem; font-weight: 700;
    margin-bottom: 1.5rem;
}

h1 {
    font-family: 'Amiri', serif;
    font-size: clamp(2.5rem, 5vw, 4.8rem);
    line-height: 1.1;
    font-weight: 700;
    margin-bottom: 2rem;
    color: var(--primary);
}
h1 em { font-style: normal; color: var(--gold); position: relative; display: inline-block; }
h1 em::after {
    content: '';
    position: absolute; bottom: 10px; left: 0; width: 100%; height: 8px;
    background: var(--gold-soft); z-index: -1;
}

.hero-sub {
    font-size: 1.2rem; color: var(--text-muted);
    max-width: 550px; margin-bottom: 3rem;
}

.btn-group { display: flex; gap: 1.5rem; align-items: center; }
.btn-primary {
    padding: 1rem 2.5rem; background: var(--primary); color: white;
    border-radius: 100px; font-weight: 700; text-decoration: none;
    transition: 0.3s;
}
.btn-primary:hover { background: var(--gold); transform: translateY(-3px); box-shadow: var(--shadow-lg); }

.btn-secondary {
    display: flex; align-items: center; gap: 0.8rem;
    color: var(--primary); font-weight: 700; text-decoration: none;
    transition: 0.3s;
}
.btn-secondary:hover { color: var(--gold); transform: translateX(-5px); }

/* STATS */
.hero-stats {
    display: flex; gap: 3rem; margin-top: 4rem;
    padding-top: 3rem; border-top: 1px solid var(--border);
}
.stat-item h3 { font-size: 2.2rem; font-weight: 800; color: var(--primary); font-family: 'Outfit'; }
.stat-item p { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }

/* SCAN ANIMATION */
.hero-visual { position: relative; display: flex; justify-content: center; }
.scanner-box {
    width: 400px; height: 400px;
    background: white;
    border-radius: 40px;
    box-shadow: var(--shadow-lg);
    display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden;
    border: 1px solid var(--border);
}
.scanner-line {
    position: absolute; top: 0; left: 0; width: 100%; height: 3px;
    background: linear-gradient(90deg, transparent, var(--gold), transparent);
    box-shadow: 0 0 15px var(--gold);
    animation: scan 4s ease-in-out infinite;
}
@keyframes scan { 0%, 100% { top: 0%; opacity: 0; } 10%, 90% { opacity: 1; } 50% { top: 100%; } }

.scanner-box img { width: 60%; filter: grayscale(1); opacity: 0.2; transition: 0.5s; }
.scanner-box:hover img { filter: grayscale(0); opacity: 1; }

/* SECTIONS */
section { padding: 120px 5%; }
.section-header { text-align: center; max-width: 800px; margin: 0 auto 80px; }
.section-label { color: var(--gold); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; margin-bottom: 1rem; display: block; }
.section-title { font-family: 'Amiri', serif; font-size: 3.2rem; color: var(--primary); line-height: 1.2; }

/* FEATURES */
.features-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 2.5rem; max-width: 1300px; margin: 0 auto;
}
.feature-card {
    background: white; border: 1px solid var(--border);
    padding: 3.5rem 2.5rem; border-radius: 24px;
    transition: all 0.4s ease;
}
.feature-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
.feat-icon {
    width: 70px; height: 70px; background: var(--bg-alt);
    border-radius: 20px; display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; color: var(--gold); margin-bottom: 2rem;
    transition: 0.4s;
}
.feature-card:hover .feat-icon { background: var(--gold); color: white; transform: rotateY(180deg); }
.feature-card h3 { font-size: 1.5rem; margin-bottom: 1.2rem; color: var(--primary); }
.feature-card p { color: var(--text-muted); font-size: 1.05rem; }

/* STEPS */
#how-it-works { background: var(--bg-alt); }
.steps-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 2rem; position: relative;
}
.step-card { text-align: center; position: relative; }
.step-num {
    width: 60px; height: 60px; background: white;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 2rem; font-family: 'Outfit'; font-weight: 800; font-size: 1.4rem;
    color: var(--gold); box-shadow: var(--shadow-md);
    position: relative; z-index: 2;
}
.step-card h4 { font-size: 1.3rem; margin-bottom: 1rem; color: var(--primary); }
.step-card p { font-size: 0.95rem; color: var(--text-muted); }

/* FAQ */
.faq-container { max-width: 900px; margin: 0 auto; }
.faq-item {
    border-bottom: 1px solid var(--border);
    margin-bottom: 1rem;
}
.faq-header {
    width: 100%; padding: 1.5rem 0;
    display: flex; align-items: center; justify-content: space-between;
    background: none; border: none; cursor: pointer;
    font-size: 1.2rem; font-weight: 700; color: var(--primary); text-align: right;
}
.faq-body { padding: 0 0 1.5rem; color: var(--text-muted); display: none; line-height: 1.8; }
.faq-item.active .faq-body { display: block; }
.faq-item.active .faq-header { color: var(--gold); }

/* APP CTA */
.app-section {
    background: var(--primary);
    border-radius: 40px; padding: 100px 5%; margin: 60px 5%;
    text-align: center; color: white;
    position: relative; overflow: hidden;
}
.app-section h2 { font-family: 'Amiri', serif; font-size: 3.5rem; margin-bottom: 1.5rem; }
.app-section p { font-size: 1.2rem; opacity: 0.8; margin-bottom: 3rem; max-width: 600px; margin-inline: auto; }
.app-btns { display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; }
.app-btns img { height: 55px; transition: 0.3s; }
.app-btns img:hover { transform: scale(1.08); }

/* FOOTER */
footer { padding: 100px 5% 50px; border-top: 1px solid var(--border); background: #FFF; }
.footer-content {
    display: grid; grid-template-columns: 2fr 1fr 1fr;
    gap: 5rem; max-width: 1400px; margin: 0 auto;
}
.footer-brand h2 { font-family: 'Amiri', serif; font-size: 2.2rem; color: var(--gold); margin-bottom: 1.5rem; }
.footer-brand p { color: var(--text-muted); max-width: 350px; }
.footer-links h4 { font-size: 1.1rem; margin-bottom: 2rem; color: var(--primary); }
.footer-links ul { list-style: none; }
.footer-links li { margin-bottom: 1rem; }
.footer-links a { text-decoration: none; color: var(--text-muted); transition: 0.3s; font-weight: 500; }
.footer-links a:hover { color: var(--gold); padding-right: 5px; }

.footer-bottom {
    margin-top: 80px; padding-top: 40px; border-top: 1px solid var(--border);
    text-align: center; color: var(--text-muted); font-size: 0.9rem;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
    .hero-content { grid-template-columns: 1fr; text-align: center; }
    .hero-sub { margin-inline: auto; }
    .btn-group { justify-content: center; }
    .hero-stats { justify-content: center; }
    .features-grid { grid-template-columns: repeat(2, 1fr); }
    .steps-grid { grid-template-columns: repeat(2, 1fr); }
    .footer-content { grid-template-columns: 1fr; text-align: center; }
    .footer-brand p { margin-inline: auto; }
}
@media (max-width: 768px) {
    nav ul { display: none; }
    .features-grid { grid-template-columns: 1fr; }
    .steps-grid { grid-template-columns: 1fr; }
    h1 { font-size: 3rem; }
    .section-title { font-size: 2.5rem; }
    .scanner-box { width: 100%; max-width: 350px; height: 350px; }
}
</style>
</head>
<body>

<nav id="navbar">
  <a href="{{ route('home') }}" class="logo">
    <img src="{{ asset('assets/img/Logo.png') }}" alt="Thamn Logo">
    <span>ثمن</span>
  </a>
  <ul>
    <li><a href="#hero">الرئيسية</a></li>
    <li><a href="#features">المميزات</a></li>
    <li><a href="#how-it-works">كيف نعمل</a></li>
    <li><a href="#faq">الأسئلة الشائعة</a></li>
  </ul>
  <div class="nav-btns">
      <a href="{{ route('experts.register') }}" class="nav-cta">انضم كخبير</a>
  </div>
</nav>

<!-- HERO -->
<section id="hero">
    <div class="hero-content">
        <div class="hero-text" data-aos="fade-up">
            <div class="hero-tag">
                <i class="fas fa-certificate"></i>
                نظام تقييم رقمي معتمد عالمياً
            </div>
            <h1>قيمة مقتنياتك <em>تستحق</em> المعرفة الحقيقية</h1>
            <p class="hero-sub">
                منصة ثمن تمنحك الثقة في معرفة قيمة أصولك الفاخرة عبر دمج عبقرية الذكاء الاصطناعي مع خبرة نخبة المثمنين المعتمدين.
            </p>
            <div class="btn-group">
                <a href="#" class="btn-primary">ابدأ التقييم الآن</a>
                <a href="#" class="btn-secondary">
                    <i class="fas fa-play-circle"></i>
                    شاهد كيف يعمل
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <h3 data-count="99">0</h3>
                    <p>دقة التقييم %</p>
                </div>
                <div class="stat-item">
                    <h3 data-count="45">0</h3>
                    <p>خبير عالمي</p>
                </div>
                <div class="stat-item">
                    <h3 data-count="15">0</h3>
                    <p>ألف عملية تثمين</p>
                </div>
            </div>
        </div>
        <div class="hero-visual" data-aos="zoom-in" data-aos-delay="200">
            <div class="scanner-box">
                <div class="scanner-line"></div>
                <img src="{{ asset('assets/img/Logo.png') }}" alt="Scanning Asset">
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features">
    <div class="section-header" data-aos="fade-up">
        <span class="section-label">لماذا ثمن؟</span>
        <h2 class="section-title">المكان الأمثل لمعرفة <em>القيمة العادلة</em></h2>
    </div>
    <div class="features-grid">
        <div class="feature-card" data-aos="fade-up">
            <div class="feat-icon"><i class="fas fa-microchip"></i></div>
            <h3>ذكاء اصطناعي فوري</h3>
            <p>محركنا الذكي يحلل ملايين البيانات السوقية والمزادات العالمية في أجزاء من الثانية.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
            <div class="feat-icon"><i class="fas fa-user-tie"></i></div>
            <h3>خبراء معتمدون</h3>
            <p>مراجعة دقيقة من متخصصين دوليين لضمان تغطية كافة التفاصيل التي قد تغيب عن الآلة.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
            <div class="feat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <h3>تقارير رسمية</h3>
            <p>احصل على مستندات تقييم معترف بها لدى شركات التأمين والبنوك والجهات القانونية.</p>
        </div>
        <div class="feature-card" data-aos="fade-up">
            <div class="feat-icon"><i class="fas fa-gem"></i></div>
            <h3>تعدد التخصصات</h3>
            <p>من الساعات والمجوهرات إلى السيارات الكلاسيكية والتحف الفنية النادرة.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
            <div class="feat-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>أمان وخصوصية</h3>
            <p>تشفير عالي المستوى لبياناتك وصور مقتنياتك مع ضمان سرية تامة لكل معاملة.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
            <div class="feat-icon"><i class="fas fa-chart-line"></i></div>
            <h3>مراقبة السوق</h3>
            <p>لوحة تحكم ذكية تتيح لك مراقبة تغير قيمة مقتنياتك مع تقلبات الأسواق العالمية.</p>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how-it-works">
    <div class="section-header" data-aos="fade-up">
        <span class="section-label">آلية العمل</span>
        <h2 class="section-title">خطوات بسيطة نحو <em>الحقيقة</em></h2>
    </div>
    <div class="steps-grid">
        <div class="step-card" data-aos="fade-right">
            <div class="step-num">01</div>
            <h4>رفع البيانات</h4>
            <p>التقط صوراً لمقتنياتك وارفعها مع أي وثائق متوفرة.</p>
        </div>
        <div class="step-card" data-aos="fade-right" data-aos-delay="100">
            <div class="step-num">02</div>
            <h4>الفحص الرقمي</h4>
            <p>يقوم النظام بمطابقة القطعة مع قواعد بياناتنا العالمية.</p>
        </div>
        <div class="step-card" data-aos="fade-right" data-aos-delay="200">
            <div class="step-num">03</div>
            <h4>تدقيق الخبير</h4>
            <p>يتم تحويل الطلب للمتخصص المناسب لإعطاء رأيه الفني.</p>
        </div>
        <div class="step-card" data-aos="fade-right" data-aos-delay="300">
            <div class="step-num">04</div>
            <h4>النتيجة النهائية</h4>
            <p>استلم تقريرك الشامل والموقع إلكترونياً خلال ساعات.</p>
        </div>
    </div>
</section>

<!-- FAQ -->
<section id="faq">
    <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">أسئلة <em>شائعة</em></h2>
    </div>
    <div class="faq-container">
        <div class="faq-item" data-aos="fade-up">
            <button class="faq-header">هل التقييم مقبول لدى الجهات الرسمية؟ <i class="fas fa-plus"></i></button>
            <div class="faq-body">نعم، التقارير الصادرة عن فئة "الهجين" والخبراء تعتمد على معايير التقييم الدولية وتعتبر مرجعاً قوياً لشركات التأمين والمحاكم.</div>
        </div>
        <div class="faq-item" data-aos="fade-up">
            <button class="faq-header">ما هي تكلفة عملية التثمين؟ <i class="fas fa-plus"></i></button>
            <div class="faq-body">تختلف التكلفة حسب نوع الخدمة (AI فقط، هجين، أو خبير متخصص) وحسب نوع المقتنيات، ويمكنك رؤية الأسعار بوضوح داخل التطبيق.</div>
        </div>
        <div class="faq-item" data-aos="fade-up">
            <button class="faq-header">كيف يتم حماية صوري وبياناتي؟ <i class="fas fa-plus"></i></button>
            <div class="faq-body">نستخدم بروتوكولات تشفير بنكية لحماية كافة الصور والبيانات المرفوعة، ولا يتم مشاركتها مع أي طرف ثالث خارج عملية التقييم.</div>
        </div>
    </div>
</section>

<!-- APP SECTION -->
<div class="app-section" data-aos="zoom-out">
    <h2>ابدأ رحلتك مع ثمن</h2>
    <p>انضم إلى آلاف المستخدمين الذين يثقون بنا لمعرفة قيمة أصولهم. حمل التطبيق الآن واستمتع بالدقة.</p>
    <div class="app-btns">
        <a href="#"><img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store"></a>
        <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Play Store"></a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-content">
        <div class="footer-brand">
            <h2>ثمن</h2>
            <p>المنصة الرائدة في تثمين المقتنيات الثمينة والأصول الفاخرة باستخدام أحدث تقنيات الذكاء الاصطناعي وخبرات المثمنين المعتمدين.</p>
        </div>
        <div class="footer-links">
            <h4>روابط هامة</h4>
            <ul>
                <li><a href="#">عن المنصة</a></li>
                <li><a href="#">سياسة الخصوصية</a></li>
                <li><a href="#">الشروط والأحكام</a></li>
                <li><a href="#">تواصل معنا</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>تواصل معنا</h4>
            <ul>
                <li><a href="#"><i class="fab fa-twitter"></i> تويتر</a></li>
                <li><a href="#"><i class="fab fa-instagram"></i> انستجرام</a></li>
                <li><a href="#"><i class="fab fa-linkedin"></i> لينكد إن</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 منصة ثمن. جميع الحقوق محفوظة.
    </div>
</footer>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    /* Init Animations */
    AOS.init({ duration: 800, once: true });

    /* Navbar Scroll */
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('navbar');
        if(window.scrollY > 50) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');
    });

    /* Counter Animation */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('h3');
                counters.forEach(c => {
                    const target = +c.getAttribute('data-count');
                    let count = 0;
                    const speed = target / 50;
                    const update = () => {
                        count += speed;
                        if(count < target) {
                            c.innerText = Math.floor(count);
                            requestAnimationFrame(update);
                        } else { c.innerText = target; }
                    }
                    update();
                });
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    observer.observe(document.querySelector('.hero-stats'));

    /* FAQ Toggle */
    document.querySelectorAll('.faq-header').forEach(header => {
        header.addEventListener('click', () => {
            const item = header.parentElement;
            item.classList.toggle('active');
            const icon = header.querySelector('i');
            icon.classList.toggle('fa-plus');
            icon.classList.toggle('fa-minus');
        });
    });
</script>
</body>
</html>
