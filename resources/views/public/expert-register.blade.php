<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انضم كخبير — تطبيق ثمن</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        :root {
            --bg: #FFFFFF;
            --bg-alt: #F9F9FB;
            --primary: #1A1A1A;
            --secondary: #555555;
            --gold: #FF9F0A;
            --gold-light: #FFA521;
            --gold-soft: rgba(255, 159, 10, 0.1);
            --text-main: #1A1A1A;
            --text-muted: #666666;
            --border: rgba(0, 0, 0, 0.08);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.1);
        }

        html {
            scroll-behavior: smooth
        }

        body {
            background: var(--bg-alt);
            color: var(--text-main);
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            line-height: 1.6;
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1rem 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            color: inherit;
        }

        .logo img {
            height: 45px;
        }

        .logo span {
            color: var(--primary);
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 2.5rem
        }

        nav ul a {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--secondary);
            text-decoration: none;
            transition: 0.3s;
        }

        nav ul a:hover {
            color: var(--gold)
        }

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
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 2rem;
            line-height: 1.2;
            font-weight: 800;
        }

        .reg-info h1 em {
            font-style: normal;
            color: var(--gold);
        }

        .reg-info p {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
        }

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

        .benefit-item:hover {
            border-color: var(--gold);
            transform: translateX(-10px);
            box-shadow: var(--shadow-md);
        }

        .benefit-icon {
            width: 50px;
            height: 50px;
            background: var(--gold-soft);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .benefit-text h4 {
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 0.3rem;
        }

        .benefit-text p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }

        /* FORM CARD */
        .form-card {
            background: white;
            padding: 4rem;
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
        }

        .form-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .form-header h2 {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            font-size: 2.2rem;
            color: var(--primary);
            font-weight: 800;
        }

        .form-header p {
            color: var(--text-muted);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full {
            grid-column: span 2;
        }

        label {
            display: block;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 0.6rem;
            color: var(--primary);
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%231A1A1A%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: left 1rem center;
            background-size: 0.7em auto;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--gold);
            background-color: white;
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

        .submit-btn:hover {
            background: var(--gold);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(193, 149, 62, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* FOOTER */
        footer {
            padding: 80px 5% 40px;
            background: white;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .footer-logo {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            font-size: 2rem;
            color: var(--gold);
            margin-bottom: 1rem;
            display: block;
            text-decoration: none;
            font-weight: 800;
        }

        .footer-copy {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .reg-section {
                grid-template-columns: 1fr;
            }

            .reg-info {
                text-align: center;
            }

            .benefit-item {
                text-align: right;
            }

            .form-card {
                padding: 3rem 2rem;
            }
        }

        @media (max-width: 768px) {
            nav ul {
                display: none;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: span 1;
            }

            .reg-info h1 {
                font-size: 2.8rem;
            }
        }

        /* ===== ATTACHMENTS SECTION (MANDATORY) ===== */
        .attachments-section-wrapper {
            background: #ffffff;
            border: 2px solid rgba(255, 159, 10, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin: 1.5rem 0;
            box-shadow: 0 10px 30px rgba(255, 159, 10, 0.06);
            position: relative;
        }

        .attachments-notice-card {
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
            background: linear-gradient(135deg, rgba(255, 159, 10, 0.12) 0%, rgba(255, 159, 10, 0.03) 100%);
            border: 1.5px solid rgba(255, 159, 10, 0.35);
            border-radius: 14px;
            padding: 1.2rem 1.4rem;
            margin-bottom: 1.6rem;
        }

        .notice-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--gold);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(255, 159, 10, 0.3);
        }

        .notice-content h4 {
            font-size: 1.15rem;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 0.3rem;
        }

        .notice-content p {
            color: #444;
            font-size: 0.98rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .badge-mandatory {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: #d97706;
            border: 1px solid rgba(217, 119, 6, 0.3);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .dropzone-area {
            border: 2px dashed rgba(255, 159, 10, 0.5);
            border-radius: 16px;
            background: var(--bg-alt);
            padding: 2.2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dropzone-area:hover,
        .dropzone-area.dragover {
            border-color: var(--gold);
            background: rgba(255, 159, 10, 0.06);
            transform: translateY(-2px);
        }

        .dropzone-icon {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 0.8rem;
        }

        .dropzone-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.3rem;
        }

        .dropzone-subtitle {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .dropzone-hints {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            flex-wrap: wrap;
            margin-bottom: 1.2rem;
        }

        .dropzone-hints span {
            font-size: 0.82rem;
            color: #666;
            background: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-choose-files {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            padding: 0.75rem 1.8rem;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.25s;
        }

        .btn-choose-files:hover {
            background: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(255, 159, 10, 0.3);
        }

        .uploaded-attachments-list {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .attachment-item-card {
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 1.2rem;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .attachment-item-card:hover {
            border-color: rgba(255, 159, 10, 0.6);
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }

        .attachment-item-card.has-error {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.02);
        }

        .attachment-item-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 0.9rem;
        }

        .attachment-info {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            overflow: hidden;
            flex: 1;
        }

        .attachment-badge-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .pdf-badge {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .img-badge {
            background: rgba(59, 130, 246, 0.12);
            color: #3b82f6;
        }

        .attachment-name-meta {
            overflow: hidden;
        }

        .attachment-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .attachment-meta {
            font-size: 0.8rem;
            color: #777;
            margin-top: 2px;
        }

        .btn-delete-file {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-delete-file:hover {
            background: #ef4444;
            color: #ffffff;
            transform: scale(1.05);
        }

        .attachment-type-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .attachment-type-label {
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            margin: 0;
        }

        .attachment-type-select {
            flex: 1;
            min-width: 240px;
            background: var(--bg-alt);
            border: 1.5px solid var(--border);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--primary);
        }

        .attachment-type-select:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 3px var(--gold-soft);
        }

        .btn-add-more {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1.5px dashed var(--gold);
            color: var(--gold);
            padding: 0.7rem 1.4rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.92rem;
            cursor: pointer;
            transition: all 0.25s;
        }

        .btn-add-more:hover {
            background: var(--gold);
            color: #ffffff;
        }
    </style>
</head>

<body>

    <nav>
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('assets/img/Logo-black.png') }}" alt="Thamn Logo">
            <span>ثمن</span>
        </a>
        <ul>
            <li><a href="{{ route('home') }}">الرئيسية</a></li>
            <li><a href="{{ route('home') }}#features">المميزات</a></li>
            <li><a href="{{ route('home') }}#how-it-works">كيف نعمل</a></li>
        </ul>
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <a href="{{ route('expert.login') }}" class="nav-cta" style="background: transparent; border: 1.5px solid var(--gold); color: var(--primary);">دخول الخبراء</a>
            <a href="{{ route('home') }}" class="nav-cta">العودة للرئيسية</a>
        </div>
    </nav>

    <section class="reg-section">
        <div class="reg-info" data-aos="fade-left">
            <h1>انضم إلى فريق <em>خبراء ثمن</em></h1>
            <p>نحن نبحث عن أفضل الكفاءات في مجالات التقييم الفني والتقني. انضم إلينا وساهم في بناء مستقبل التثمين الذكي.
            </p>

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
                        <label>رقم الجوال <span style="color:red;">*</span></label>
                        <input type="text" name="phone" placeholder="05xxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label>الجنسية <span style="color:red;">*</span></label>
                        <select name="nationality" required>
                            <option value="" disabled selected>اختر الجنسية</option>
                            <optgroup label="دول الخليج العربي">
                                <option value="سعودي">سعودي</option>
                                <option value="إماراتي">إماراتي</option>
                                <option value="كويتي">كويتي</option>
                                <option value="بحريني">بحريني</option>
                                <option value="عماني">عماني</option>
                                <option value="قطري">قطري</option>
                            </optgroup>
                            <optgroup label="الدول العربية">
                                <option value="مصري">مصري</option>
                                <option value="أردني">أردني</option>
                                <option value="سوري">سوري</option>
                                <option value="لبناني">لبناني</option>
                                <option value="فلسطيني">فلسطيني</option>
                                <option value="يمني">يمني</option>
                                <option value="سوداني">سوداني</option>
                                <option value="عراقي">عراقي</option>
                                <option value="مغربي">مغربي</option>
                                <option value="جزائري">جزائري</option>
                                <option value="تونسي">تونسي</option>
                                <option value="ليبي">ليبي</option>
                                <option value="موريتاني">موريتاني</option>
                                <option value="صومالي">صومالي</option>
                            </optgroup>
                            <optgroup label="جنسيات أخرى">
                                <option value="تركي">تركي</option>
                                <option value="باكستاني">باكستاني</option>
                                <option value="هندي">هندي</option>
                                <option value="بنغلاديشي">بنغلاديشي</option>
                                <option value="فلبيني">فلبيني</option>
                                <option value="ماليزي">ماليزي</option>
                                <option value="إندونيسي">إندونيسي</option>
                                <option value="بريطاني">بريطاني</option>
                                <option value="أمريكي">أمريكي</option>
                                <option value="كندي">كندي</option>
                                <option value="فرنسي">فرنسي</option>
                                <option value="ألماني">ألماني</option>
                                <option value="إيطالي">إيطالي</option>
                                <option value="أخرى">أخرى</option>
                            </optgroup>
                        </select>
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
                        <label>القسم الرئيسي للتقييم <span style="color:red;">*</span></label>
                        <select name="category_id" required>
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
                    <!-- Attachments Section (Mandatory) -->
                    <div class="form-group full attachments-section-wrapper" id="attachmentsSection">
                        
                        <!-- Alert / Notice Box -->
                        <div class="attachments-notice-card">
                            <div class="notice-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="notice-content">
                                <h4>عزيزي الخبير :</h4>
                                <p>رفعك للمرفقات يسهل عليك سرعة اعتماد العضوية وتوثيقها</p>
                                <span class="badge-mandatory"><i class="fas fa-asterisk"></i> المرفقات إجبارية لإتمام وتوثيق العضوية</span>
                            </div>
                        </div>

                        <!-- Upload Box / Dropzone -->
                        <div class="dropzone-area" id="dropzoneArea">
                            <input type="file" id="attachmentsFileInput" multiple accept=".pdf,image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
                            <div class="dropzone-icon">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>
                            <h3 class="dropzone-title">ارفع المرفقات هنا</h3>
                            <p class="dropzone-subtitle">انقر لاختيار الملفات أو اسحبها وأفلتها هنا</p>
                            <div class="dropzone-hints">
                                <span><i class="fas fa-file-pdf"></i> ملفات PDF</span>
                                <span><i class="fas fa-file-image"></i> صور (JPG, PNG, WEBP)</span>
                                <span><i class="fas fa-database"></i> حتى 10 ميجابايت للمرفق</span>
                            </div>
                            <button type="button" class="btn-choose-files" id="btnChooseFiles">
                                <i class="fas fa-folder-open"></i> اختيار المرفقات
                            </button>
                        </div>

                        <!-- Uploaded Files List with Type Selector -->
                        <div class="uploaded-attachments-list" id="uploadedAttachmentsList">
                            <!-- Cards will be dynamically added here -->
                        </div>

                        <!-- Add More Button (visible when files are present) -->
                        <div id="addMoreWrapper" style="display: none; margin-top: 1.2rem; text-align: right;">
                            <button type="button" class="btn-add-more" id="btnAddMore">
                                <i class="fas fa-plus-circle"></i> إضافة مرفق آخر
                            </button>
                        </div>

                    </div>
                    <div class="form-group full">
                        <label>الخبرات السابقة</label>
                        <textarea name="experience" rows="3"
                            placeholder="اشرح باختصار خبراتك في مجال التقييم..."></textarea>
                    </div>
                    <div class="form-group full">
                        <label>المؤهلات والشهادات</label>
                        <textarea name="certificates" rows="2"
                            placeholder="الشهادات المهنية أو الدورات الحاصل عليها..."></textarea>
                    </div>
                    <div class="form-group full"
                        style="display: flex; align-items: center; gap: 0.8rem; margin-top: 1rem;">
                        <input type="checkbox" id="terms_check" required
                            style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="terms_check" style="margin-bottom: 0; font-weight: 500; cursor: pointer;">
                            أوافق على <a href="{{ route('experts.agreement.preview') }}" target="_blank"
                                style="color: var(--gold); text-decoration: none; font-weight: 700;">الاتفاقية القانونية
                                للتعاون مع الخبير</a>
                            الخاصة بالمنصة
                        </label>
                    </div>
                </div>
                <button type="submit" class="submit-btn" id="submitBtn">إرسال الطلب</button>
                <div style="text-align: center; margin-top: 1.2rem; font-size: 0.95rem; color: #666;">
                    لديك حساب خبير مسجل بالفعل؟
                    <a href="{{ route('expert.login') }}" style="color: var(--gold); font-weight: 700; text-decoration: none;">تسجيل الدخول</a>
                </div>
            </form>
        </div>
    </section>

    <footer>
        <a href="{{ route('home') }}" class="footer-logo">ثمن</a>
        <p class="footer-copy">&copy; 2025 تطبيق ثمن. جميع الحقوق محفوظة.</p>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        const form = document.getElementById('expertRegisterForm');
        const submitBtn = document.getElementById('submitBtn');
        const dropzoneArea = document.getElementById('dropzoneArea');
        const fileInput = document.getElementById('attachmentsFileInput');
        const btnChooseFiles = document.getElementById('btnChooseFiles');
        const btnAddMore = document.getElementById('btnAddMore');
        const addMoreWrapper = document.getElementById('addMoreWrapper');
        const attachmentsList = document.getElementById('uploadedAttachmentsList');

        // Store selected attachments: [{ id, file, type }]
        let expertAttachments = [];

        // Open file picker
        btnChooseFiles.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        btnAddMore.addEventListener('click', () => {
            fileInput.click();
        });

        dropzoneArea.addEventListener('click', (e) => {
            if (e.target.tagName !== 'BUTTON') {
                fileInput.click();
            }
        });

        // Drag & Drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.remove('dragover');
            }, false);
        });

        dropzoneArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFilesSelection(files);
        });

        fileInput.addEventListener('change', () => {
            handleFilesSelection(fileInput.files);
            fileInput.value = ''; // Reset input to allow re-selecting same file if needed
        });

        function formatBytes(bytes, decimals = 1) {
            if (!+bytes) return '0 B';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['بايت', 'ك.ب', 'م.ب', 'ج.ب'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }

        function handleFilesSelection(files) {
            if (!files || files.length === 0) return;

            const allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
            const maxBytes = 10 * 1024 * 1024; // 10MB

            Array.from(files).forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase();
                if (!allowedExts.includes(ext)) {
                    Swal.fire({
                        title: 'نوع ملف غير مدعوم',
                        text: `الملف "${file.name}" غير مدعوم. يرجى رفع ملفات PDF أو صور فقط.`,
                        icon: 'warning',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#C1953E'
                    });
                    return;
                }

                if (file.size > maxBytes) {
                    Swal.fire({
                        title: 'حجم الملف كبير',
                        text: `الملف "${file.name}" يتجاوز الحد الأقصى المسموح (10 ميجابايت).`,
                        icon: 'warning',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#C1953E'
                    });
                    return;
                }

                const uniqueId = 'att_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
                expertAttachments.push({
                    id: uniqueId,
                    file: file,
                    type: ''
                });
            });

            renderAttachmentsList();
        }

        function renderAttachmentsList() {
            attachmentsList.innerHTML = '';

            if (expertAttachments.length === 0) {
                addMoreWrapper.style.display = 'none';
                return;
            }

            addMoreWrapper.style.display = 'block';

            expertAttachments.forEach((item, index) => {
                const isPdf = item.file.name.toLowerCase().endsWith('.pdf');
                const card = document.createElement('div');
                card.className = 'attachment-item-card' + (!item.type ? '' : '');
                card.id = `card_${item.id}`;

                card.innerHTML = `
                    <div class="attachment-item-top">
                        <div class="attachment-info">
                            <div class="attachment-badge-icon ${isPdf ? 'pdf-badge' : 'img-badge'}">
                                <i class="fas ${isPdf ? 'fa-file-pdf' : 'fa-file-image'}"></i>
                            </div>
                            <div class="attachment-name-meta">
                                <div class="attachment-name" title="${item.file.name}">${index + 1}. ${item.file.name}</div>
                                <div class="attachment-meta">${formatBytes(item.file.size)}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-delete-file" data-id="${item.id}" title="حذف هذا المرفق">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="attachment-type-container">
                        <label class="attachment-type-label">
                            <i class="fas fa-tag" style="color: var(--gold);"></i> حدد نوع المرفق : <span style="color:red;">*</span>
                        </label>
                        <select class="attachment-type-select" data-id="${item.id}" required>
                            <option value="" disabled ${!item.type ? 'selected' : ''}>-- اضغط هنا لاختيار نوع المرفق --</option>
                            <option value="شهادة" ${item.type === 'شهادة' ? 'selected' : ''}>شهادة</option>
                            <option value="دورة" ${item.type === 'دورة' ? 'selected' : ''}>دورة</option>
                            <option value="وثيقة عمل حر" ${item.type === 'وثيقة عمل حر' ? 'selected' : ''}>وثيقة عمل حر</option>
                            <option value="سجل تجاري" ${item.type === 'سجل تجاري' ? 'selected' : ''}>سجل تجاري</option>
                            <option value="مشهد" ${item.type === 'مشهد' ? 'selected' : ''}>مشهد</option>
                            <option value="تعريف عمل" ${item.type === 'تعريف عمل' ? 'selected' : ''}>تعريف عمل</option>
                            <option value="خبرة" ${item.type === 'خبرة' ? 'selected' : ''}>خبرة</option>
                            <option value="اعتماد" ${item.type === 'اعتماد' ? 'selected' : ''}>اعتماد</option>
                            <option value="تزكية" ${item.type === 'تزكية' ? 'selected' : ''}>تزكية</option>
                            <option value="أخرى" ${item.type === 'أخرى' ? 'selected' : ''}>أخرى</option>
                        </select>
                    </div>
                `;

                // Handle delete
                card.querySelector('.btn-delete-file').addEventListener('click', () => {
                    removeAttachment(item.id);
                });

                // Handle type change
                const selectElem = card.querySelector('.attachment-type-select');
                selectElem.addEventListener('change', (e) => {
                    item.type = e.target.value;
                    card.classList.remove('has-error');
                });

                attachmentsList.appendChild(card);
            });
        }

        function removeAttachment(id) {
            expertAttachments = expertAttachments.filter(item => item.id !== id);
            renderAttachmentsList();
        }

        // Form Submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // 1. Check if at least one attachment exists (Mandatory)
            if (expertAttachments.length === 0) {
                Swal.fire({
                    title: 'المرفقات إجبارية!',
                    html: 'عزيزي الخبير : <br><strong>رفعك للمرفقات يسهل عليك سرعة اعتماد العضوية وتوثيقها.</strong><br>يرجى رفع مرفق واحد على الأقل للمتابعة.',
                    icon: 'warning',
                    confirmButtonText: 'حسناً، سأرفع المرفقات',
                    confirmButtonColor: '#C1953E'
                }).then(() => {
                    document.getElementById('attachmentsSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
                return;
            }

            // 2. Check if all attachments have an assigned type
            let unassigned = false;
            expertAttachments.forEach(item => {
                const card = document.getElementById(`card_${item.id}`);
                if (!item.type) {
                    unassigned = true;
                    if (card) {
                        card.classList.add('has-error');
                    }
                } else if (card) {
                    card.classList.remove('has-error');
                }
            });

            if (unassigned) {
                Swal.fire({
                    title: 'يرجى تحديد نوع المرفق',
                    text: 'يرجى تحديد نوع المرفق (شهادة / دورة / وثيقة عمل حر / ...) لكل ملف تم رفعه.',
                    icon: 'warning',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#C1953E'
                }).then(() => {
                    const firstError = document.querySelector('.attachment-item-card.has-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerText = 'جاري إرسال الطلب والمرفقات...';

            const formData = new FormData(form);

            // Append all attachments and their types
            expertAttachments.forEach(item => {
                formData.append('attachments[]', item.file);
                formData.append('attachment_types[]', item.type);
            });

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
                        title: 'تم إرسال الطلب بنجاح!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'تم',
                        confirmButtonColor: '#C1953E'
                    });
                    form.reset();
                    expertAttachments = [];
                    renderAttachmentsList();
                } else {
                    let errorMsg = result.message || 'حدث خطأ ما، يرجى المحاولة لاحقاً';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('<br>');
                    } else if (result.error) {
                        errorMsg += '<br><br><small style="color:red;" dir="ltr">' + result.error + '</small>';
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
                    title: 'خطأ في الاتصال',
                    text: 'فشل الاتصال بالخادم، يرجى التحقق من اتصال الإنترنت والمحاولة ثانية.',
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