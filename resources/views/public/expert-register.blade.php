<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>انضم كخبير — منصة ثمن</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
--border:rgba(0,0,0,0.08);
--shadow-md:0 10px 30px rgba(0,0,0,0.06);
--shadow-lg:0 20px 50px rgba(0,0,0,0.1);
}
html{scroll-behavior:smooth}
body{
  background:var(--bg-alt);
  color:var(--text-main);
  font-family:'Cairo',sans-serif;
  line-height:1.6;
}

/* NAV */
nav{
  position:fixed;top:0;left:0;right:0;z-index:1000;
  padding:1rem 5%;
  display:flex;align-items:center;justify-content:space-between;
  background:rgba(255,255,255,0.9);
  backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);
}
.logo{ display:flex;align-items:center;gap:0.8rem; text-decoration: none; color: inherit; }
.logo img { height: 45px; }
.logo span{color:var(--primary); font-family: 'Amiri', serif; font-size: 1.8rem; font-weight: 700;}

nav ul{list-style:none;display:flex;gap:2.5rem}
nav ul a{
  font-size:0.95rem;font-weight:600;
  color:var(--secondary);text-decoration:none;
  transition:0.3s;
}
nav ul a:hover{color:var(--gold)}

/* REGISTRATION SECTION */
.reg-section {
    padding: 140px 5% 100px;
    display: grid;
    grid-template-columns: 0.8fr 1.2fr;
    gap: 5rem;
    max-width: 1400px;
    margin: 0 auto;
    align-items: start;
}

.reg-info h1 {
    font-family: 'Amiri', serif;
    font-size: 3.5rem;
    color: var(--primary);
    margin-bottom: 2rem;
    line-height: 1.2;
}
.reg-info h1 em { font-style: normal; color: var(--gold); }
.reg-info p { font-size: 1.15rem; color: var(--text-muted); margin-bottom: 3rem; }

.benefit-item {
    display: flex;
    gap: 1.2rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border);
    transition: 0.3s;
}
.benefit-item:hover { border-color: var(--gold); transform: translateX(-10px); box-shadow: var(--shadow-md); }
.benefit-icon {
    width: 50px; height: 50px; background: var(--gold-soft);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    color: var(--gold); font-size: 1.3rem; flex-shrink: 0;
}
.benefit-text h4 { font-size: 1.1rem; color: var(--primary); margin-bottom: 0.3rem; }
.benefit-text p { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0; }

/* FORM CARD */
.form-card {
    background: white;
    padding: 4rem;
    border-radius: 30px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border);
}
.form-header { text-align: center; margin-bottom: 3rem; }
.form-header h2 { font-family: 'Amiri', serif; font-size: 2.2rem; color: var(--primary); }
.form-header p { color: var(--text-muted); }

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}
.form-group { margin-bottom: 1.5rem; }
.form-group.full { grid-column: span 2; }

label {
    display: block;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 0.6rem;
    color: var(--primary);
}
input, textarea {
    width: 100%;
    padding: 0.9rem 1.2rem;
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: 12px;
    font-family: inherit;
    font-size: 1rem;
    transition: all 0.3s ease;
}
input:focus, textarea:focus {
    outline: none;
    border-color: var(--gold);
    background: white;
    box-shadow: 0 0 0 4px var(--gold-soft);
}

.submit-btn {
    width: 100%;
    padding: 1.2rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 100px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin-top: 1rem;
}
.submit-btn:hover { background: var(--gold); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(193, 149, 62, 0.3); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* FOOTER */
footer { padding: 80px 5% 40px; background: white; border-top: 1px solid var(--border); text-align: center; }
.footer-logo { font-family: 'Amiri', serif; font-size: 2rem; color: var(--gold); margin-bottom: 1rem; display: block; text-decoration: none; }
.footer-copy { color: var(--text-muted); font-size: 0.9rem; }

/* RESPONSIVE */
@media (max-width: 1024px) {
    .reg-section { grid-template-columns: 1fr; }
    .reg-info { text-align: center; }
    .benefit-item { text-align: right; }
    .form-card { padding: 3rem 2rem; }
}
@media (max-width: 768px) {
    nav ul { display: none; }
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full { grid-column: span 1; }
    .reg-info h1 { font-size: 2.8rem; }
}
</style>
</head>
<body>

<nav>
  <a href="{{ route('home') }}" class="logo">
    <img src="{{ asset('assets/img/Logo.png') }}" alt="Thamn Logo">
    <span>ثمن</span>
  </a>
  <ul>
    <li><a href="{{ route('home') }}">الرئيسية</a></li>
    <li><a href="{{ route('home') }}#features">المميزات</a></li>
    <li><a href="{{ route('home') }}#how-it-works">كيف نعمل</a></li>
  </ul>
  <a href="{{ route('home') }}" class="nav-cta">العودة للرئيسية</a>
</nav>

<section class="reg-section">
    <div class="reg-info" data-aos="fade-left">
        <h1>انضم إلى فريق <em>خبراء ثمن</em></h1>
        <p>نحن نبحث عن أفضل الكفاءات في مجالات التقييم الفني والتقني. انضم إلينا وساهم في بناء مستقبل التثمين الذكي.</p>
        
        <div class="benefit-item" data-aos="fade-up" data-aos-delay="100">
            <div class="benefit-icon"><i class="fas fa-wallet"></i></div>
            <div class="benefit-text">
                <h4>عائد مادي مجزي</h4>
                <p>اربح مبالغ مالية مقابل كل عملية تقييم تقوم بتدقيقها عبر المنصة.</p>
            </div>
        </div>
        
        <div class="benefit-item" data-aos="fade-up" data-aos-delay="200">
            <div class="benefit-icon"><i class="fas fa-clock"></i></div>
            <div class="benefit-text">
                <h4>مرونة تامة</h4>
                <p>اعمل في أي وقت ومن أي مكان يناسبك من خلال هاتفك المحمول.</p>
            </div>
        </div>
        
        <div class="benefit-item" data-aos="fade-up" data-aos-delay="300">
            <div class="benefit-icon"><i class="fas fa-chart-line"></i></div>
            <div class="benefit-text">
                <h4>نمو مهني</h4>
                <p>كن جزءاً من شبكة عالمية من الخبراء ووسع نطاق أعمالك وتواجدك الرقمي.</p>
            </div>
        </div>
    </div>

    <div class="form-card" data-aos="zoom-in">
        <div class="form-header">
            <h2>طلب انضمام خبير</h2>
            <p>يرجى ملء البيانات التالية وسنقوم بمراجعتها خلال 48 ساعة</p>
        </div>
        
        <form id="expertRegisterForm" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>الاسم الأول</label>
                    <input type="text" name="first_name" placeholder="أدخل اسمك الأول" required>
                </div>
                <div class="form-group">
                    <label>اسم العائلة</label>
                    <input type="text" name="last_name" placeholder="أدخل اسم العائلة" required>
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" placeholder="example@mail.com" required>
                </div>
                <div class="form-group">
                    <label>رقم الجوال</label>
                    <input type="text" name="phone" placeholder="05xxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label>اسم البنك</label>
                    <input type="text" name="bank_name" placeholder="اسم المصرف">
                </div>
                <div class="form-group">
                    <label>رقم الآيبان (IBAN)</label>
                    <input type="text" name="iban" placeholder="SAxxxxxxxxxxxxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>مجال الخبرة (نصي)</label>
                    <input type="text" name="expertise" placeholder="مثال: أثاث / سيارات / مجوهرات">
                </div>
                <div class="form-group">
                    <label>القسم الرئيسي للتقييم</label>
                    <select name="category_id" required style="width: 100%; padding: 0.9rem 1.2rem; background: var(--bg-alt); border: 1px solid var(--border); border-radius: 12px; font-family: inherit; font-size: 1rem; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%231A1A1A%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat, repeat; background-position: left .7em top 50%, 0 0; background-size: .65em auto;">
                        <option value="" disabled selected>اختر القسم الذي ستعمل فيه</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name_ar ?? $category->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>الصورة الشخصية</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <div class="form-group full">
                    <label>شهادة الخبرة (PDF أو صورة)</label>
                    <input type="file" name="experience_certificate" accept=".pdf,image/*">
                </div>
                <div class="form-group full">
                    <label>الخبرات السابقة</label>
                    <textarea name="experience" rows="3" placeholder="اشرح باختصار خبراتك في مجال التقييم..."></textarea>
                </div>
                <div class="form-group full">
                    <label>المؤهلات والشهادات</label>
                    <textarea name="certificates" rows="2" placeholder="الشهادات المهنية أو الدورات الحاصل عليها..."></textarea>
                </div>
            </div>
            <button type="submit" class="submit-btn" id="submitBtn">إرسال الطلب</button>
        </form>
    </div>
</section>

<footer>
    <a href="{{ route('home') }}" class="footer-logo">ثمن</a>
    <p class="footer-copy">&copy; 2025 منصة ثمن. جميع الحقوق محفوظة.</p>
</footer>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    AOS.init({ duration: 800, once: true });

    const form = document.getElementById('expertRegisterForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        submitBtn.disabled = true;
        submitBtn.innerText = 'جاري الإرسال...';

        const formData = new FormData(form);

        try {
            const response = await fetch("{{ route('experts.register.submit') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status) {
                Swal.fire({
                    title: 'تم الإرسال!',
                    text: result.message,
                    icon: 'success',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#C1953E'
                });
                form.reset();
            } else {
                let errorMsg = 'حدث خطأ ما، يرجى المحاولة لاحقاً';
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('<br>');
                }
                Swal.fire({
                    title: 'تنبيه',
                    html: errorMsg,
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#C1953E'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'خطأ',
                text: 'فشل الاتصال بالخادم، يرجى المحقق من الإنترنت',
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#C1953E'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'إرسال الطلب';
        }
    });
</script>
</body>
</html>
