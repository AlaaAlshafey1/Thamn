<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وثيقة الشروط والأحكام وإقرار السرية - منصة ثمن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', 'Tahoma', sans-serif;
            background: #e8e0d4;
            direction: rtl;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 20px 10px 40px;
        }

        /* ========= DOCUMENT SHELL ========= */
        .doc-shell {
            width: 100%;
            max-width: 900px;
            background: white;
            box-shadow: 0 8px 50px rgba(0,0,0,0.2);
        }

        /* ========= TOP HEADER (cream bg + watermark) ========= */
        .doc-top {
            background: #f5ede0;
            padding: 22px 32px 20px;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #c9933a;
        }

        /* Watermark "ثمن" large text behind */
        .doc-top::before {
            content: 'ثمن';
            position: absolute;
            left: 20px;
            top: -10px;
            font-size: 120px;
            font-weight: 900;
            color: rgba(180, 150, 100, 0.12);
            font-family: 'Cairo', sans-serif;
            pointer-events: none;
            line-height: 1;
        }

        /* Barcode-like icon top left */
        .barcode-logo {
            position: absolute;
            top: 12px;
            left: 24px;
        }

        .barcode-logo img {
            height: 70px;
            width: auto;
            display: block;
        }

        .doc-title-block {
            text-align: right;
            padding-left: 80px;
        }

        .doc-main-title {
            font-size: 22px;
            font-weight: 900;
            color: #1a1a1a;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .doc-main-title .gold { color: #c9933a; }

        .doc-subtitle {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
            max-width: 550px;
            margin-right: 0;
            margin-left: auto;
        }

        /* ========= CONTENT AREA ========= */
        .doc-content {
            padding: 28px 32px;
        }

        /* ========= ALERTS ========= */
        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        /* ========= FIELDS BOX ========= */
        .fields-box {
            border: 1px solid #d9cfc0;
            border-radius: 4px;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .fields-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 1px solid #d9cfc0;
        }

        .fields-row:last-child { border-bottom: none; }

        .field-cell {
            display: flex;
            align-items: baseline;
            gap: 4px;
            padding: 10px 16px;
            border-left: 1px solid #d9cfc0;
            background: #fdf9f4;
        }

        .field-cell:last-child { border-left: none; }

        .field-lbl {
            font-size: 12.5px;
            font-weight: 700;
            color: #5a4420;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .field-inp {
            flex: 1;
            border: none;
            border-bottom: 1.5px dotted #a89070;
            background: transparent;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            padding: 1px 4px;
            outline: none;
            direction: rtl;
            min-width: 0;
        }

        .field-inp:focus { border-bottom: 1.5px solid #c9933a; }

        /* ========= DECLARATION TEXT BOX ========= */
        .decl-text-box {
            border: 1.5px solid #c0a878;
            border-radius: 4px;
            padding: 16px 20px;
            margin-bottom: 24px;
            background: #fffdf8;
            font-size: 13px;
            line-height: 2;
            color: #1a1a1a;
            font-weight: 700;
            text-align: justify;
        }

        /* ========= ARTICLES TWO-COLUMN ========= */
        .articles-section { margin-bottom: 20px; }

        .article-row {
            display: flex;
            gap: 0;
            border: 1px solid #d9cfc0;
            border-radius: 4px;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .article-label-col {
            width: 175px;
            flex-shrink: 0;
            background: #fdf9f4;
            border-left: 1px solid #d9cfc0;
            padding: 14px 14px;
            font-size: 11.5px;
            font-weight: 800;
            color: #5a4420;
            line-height: 1.7;
        }

        .article-label-col .art-num {
            display: inline-block;
            background: #c9933a;
            color: white;
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 3px;
            margin-bottom: 5px;
        }

        .article-content-col {
            flex: 1;
            padding: 14px 16px;
            font-size: 12px;
            line-height: 1.85;
            color: #2c2c2c;
        }

        .article-content-col b, .article-content-col strong { color: #1a1a1a; }

        .article-content-col .sub-title {
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .article-content-col ol {
            padding-right: 16px;
        }

        .article-content-col ol li {
            margin-bottom: 5px;
        }

        .article-content-col ul {
            padding-right: 16px;
            list-style: none;
        }

        .article-content-col ul li::before { content: '• '; color: #c9933a; }
        .article-content-col ul li { margin-bottom: 3px; }

        /* how-it-works box */
        .how-box {
            background: #fdf9f4;
            border: 1px solid #e0d0b0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-top: 10px;
            font-size: 11.5px;
        }
        .how-box .how-title { font-weight: 800; color: #5a4420; margin-bottom: 6px; }

        /* ========= PROGRESS BAR ========= */
        .progress-wrap {
            height: 4px;
            background: #e8d5b0;
            border-radius: 2px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #d4af37, #c9933a);
            width: 0%;
            transition: width 0.4s ease;
            border-radius: 2px;
        }

        /* ========= READ CONFIRM ========= */
        .read-confirm {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: #fdf9f0;
            border: 1px solid #e0c888;
            border-radius: 6px;
            margin: 18px 0;
            cursor: pointer;
        }

        .read-confirm input[type="checkbox"] {
            width: 17px; height: 17px;
            accent-color: #c9933a;
            flex-shrink: 0;
            cursor: pointer;
        }

        .read-confirm label {
            font-size: 13px;
            font-weight: 700;
            color: #7a5c1e;
            cursor: pointer;
        }

        /* ========= SIGNATURE ========= */
        .sig-wrap {
            border: 1.5px dashed #c9933a;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        #signatureCanvas {
            display: block;
            width: 100%;
            height: 150px;
            background: white;
            touch-action: none;
            cursor: crosshair;
        }

        .sig-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 14px;
            background: #fdf9f0;
            border-top: 1px solid #e8d5a3;
        }

        .sig-hint { font-size: 11px; color: #aaa; }

        .btn-clear-sig {
            border: 1px solid #ddd;
            background: transparent;
            padding: 4px 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 11px;
            color: #888;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-clear-sig:hover { border-color: #e74c3c; color: #e74c3c; }

        .sig-err { font-size: 11px; color: #dc2626; display: none; padding: 3px 0; }

        /* ========= DATE ROW ========= */
        .date-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            color: #555;
            margin: 14px 0 20px;
            padding-top: 10px;
            border-top: 1px dashed #d9cfc0;
        }

        .date-val {
            display: inline-block;
            border-bottom: 1.5px dotted #999;
            padding: 0 8px;
            margin-right: 4px;
        }

        /* ========= SUBMIT ========= */
        .submit-wrap { text-align: center; padding: 8px 0 20px; }

        .btn-submit {
            padding: 13px 52px;
            background: linear-gradient(135deg, #d4af37, #c9933a);
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 5px 18px rgba(201,147,58,0.35);
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(201,147,58,0.5); }
        .btn-submit:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }

        .submit-note { font-size: 11px; color: #aaa; margin-top: 8px; }

        .spinner {
            width: 17px; height: 17px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ========= FOOTER ========= */
        .doc-footer {
            text-align: center;
            padding: 14px;
            border-top: 2px solid #c9933a;
            background: #fdf9f4;
            font-size: 11px;
            color: #999;
        }
        .doc-footer strong { color: #c9933a; }

        /* ========= ALREADY SIGNED ========= */
        .signed-block {
            text-align: center;
            padding: 40px 20px;
        }
        .signed-block .icon { font-size: 60px; display: block; margin-bottom: 12px; }
        .signed-block h2 { font-size: 22px; font-weight: 900; color: #16a34a; margin-bottom: 6px; }
        .signed-block p { font-size: 13px; color: #666; margin-bottom: 20px; line-height: 1.8; }

        .signed-info {
            background: #f8f5f0;
            border: 1px solid #e0d0b0;
            border-radius: 8px;
            padding: 14px 20px;
            max-width: 400px;
            margin: 0 auto 20px;
            text-align: right;
        }
        .signed-info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #e0cfc0;
            font-size: 13px;
        }
        .signed-info-row:last-child { border-bottom: none; }
        .signed-info-row .l { color: #999; }
        .signed-info-row .v { font-weight: 700; color: #1a1a1a; }

        .btn-dl {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 36px;
            background: linear-gradient(135deg, #d4af37, #c9933a);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 5px 18px rgba(212,175,55,0.35);
            transition: all 0.25s;
        }
        .btn-dl:hover { transform: translateY(-2px); color: white; }

        /* ========= SECTION LABEL ========= */
        .section-lbl {
            font-size: 13px;
            font-weight: 900;
            color: #5a4420;
            margin: 18px 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-lbl::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #c9933a;
            opacity: 0.4;
        }

        @media (max-width: 600px) {
            .fields-row { grid-template-columns: 1fr; }
            .field-cell { border-left: none; border-bottom: 1px solid #d9cfc0; }
            .article-row { flex-direction: column; }
            .article-label-col { width: 100%; border-left: none; border-bottom: 1px solid #d9cfc0; }
            .doc-content { padding: 16px; }
            .doc-top { padding: 16px; }
        }
    </style>
</head>
<body>

<div class="doc-shell">

    {{-- ===== TOP HEADER ===== --}}
    <div class="doc-top">

        {{-- Logo top-right --}}
        <div class="barcode-logo">
            <img src="{{ asset('assets/img/Logo.png') }}" alt="ثمن" style="height:70px; width:auto;">
        </div>

        <div class="doc-title-block">
            <div class="doc-main-title">
                وثيقة <span class="gold">الشروط والأحكام</span> وإقرار السرية للمحكمين المستقلين
            </div>
            <div class="doc-subtitle">
                تحدد هذه الوثيقة الإطار القانوني والمهني للتعاون بين التطبيق والمحكم المستقل في مجال تثمين السلع المستعملة. يرجى قراءة بنود السرية، المسؤولية المهنية، وآلية العمل بعناية قبل الموافقة والانضمام.
            </div>
        </div>

    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="doc-content">

        @if(session('error'))
            <div class="alert-err">⚠️ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-err">⚠️ {{ $errors->first() }}</div>
        @endif

        @if($declaration->isSigned())

        {{-- ===== SIGNED VIEW ===== --}}
        <div class="signed-block">
            <span class="icon">✅</span>
            <h2>تم التوقيع على الوثيقة</h2>
            <p>
                مرحباً بك {{ $user->first_name }}،<br>
                لقد أقررت بهذه الوثيقة بتاريخ <strong>{{ $declaration->signed_at->format('d/m/Y - H:i') }}</strong>
            </p>
            <div class="signed-info">
                <div class="signed-info-row"><span class="l">الاسم</span><span class="v">{{ $declaration->full_name }}</span></div>
                <div class="signed-info-row"><span class="l">رقم الهوية</span><span class="v">{{ $declaration->national_id }}</span></div>
                <div class="signed-info-row"><span class="l">تاريخ التوقيع</span><span class="v">{{ $declaration->signed_at->format('d/m/Y') }}</span></div>
            </div>
            @if($declaration->pdf_path)
            <a href="{{ route('declaration.download', ['token' => $token]) }}" class="btn-dl">
                ⬇️ تحميل نسخة PDF موقعة
            </a>
            @endif
        </div>

        @else

        {{-- ===== FORM ===== --}}

        <div class="progress-wrap">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        <form id="declarationForm"
              action="{{ route('declaration.submit', ['token' => $token]) }}"
              method="POST">
            @csrf

            <div class="fields-box">
                <div class="fields-row">
                    <div class="field-cell">
                        <span class="field-lbl">اقر أنا/</span>
                        <input type="text" name="full_name" id="full_name" class="field-inp"
                               value="{{ old('full_name', $user->first_name . ' ' . $user->last_name) }}" required>
                    </div>
                    <div class="field-cell">
                        <span class="field-lbl">الجنسية /</span>
                        <input type="text" name="nationality" id="nationality" class="field-inp"
                               placeholder="سعودي">
                    </div>
                    <div class="field-cell">
                        <span class="field-lbl">المدينة /</span>
                        <input type="text" name="city" id="city" class="field-inp"
                               placeholder="الرياض">
                    </div>
                </div>
                <div class="fields-row">
                    <div class="field-cell">
                        <span class="field-lbl">رقم الهوية /</span>
                        <input type="text" name="national_id" id="national_id" class="field-inp"
                               placeholder="1XXXXXXXXX" maxlength="10" required
                               value="{{ old('national_id') }}">
                    </div>
                    <div class="field-cell">
                        <span class="field-lbl">الجوال/</span>
                        <input type="tel" name="phone" id="phone" class="field-inp"
                               value="{{ old('phone', $user->phone) }}" required>
                    </div>
                    <div class="field-cell">
                        <span class="field-lbl">البريد الإلكتروني /</span>
                        <input type="email" name="email" id="email" class="field-inp"
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
                <div class="fields-row" style="border-bottom:none;">
                    <div class="field-cell" style="grid-column: span 3; border-left:none;">
                        <span class="field-lbl">مجال الخبرة /</span>
                        <input type="text" name="expertise" id="expertise" class="field-inp"
                               placeholder="سيارات / إلكترونيات / أجهزة">
                    </div>
                </div>
            </div>

            {{-- Declaration box --}}
            <div class="decl-text-box">
                الموقع أدناه بالاطلاع على محتوى الوثيقة والإقرار فيها بما ورد فيها بالبنود الواردة  في سياق التعاون مع التطبيق في مجال ( التثمين للسلع المستعملة) وأن يكون مؤهلاً للتحكيم التسعيري للمنتج المستعمل إما بتزكية رسمية أو أكاديمية أو فخرية وعليه إرفاق مايثبت ويعزز ذلك.
            </div>

            {{-- ARTICLES --}}
            <div class="articles-section">

                {{-- Article 1 --}}
                <div class="article-row">
                    <div class="article-label-col">
                        <span class="art-num">1</span><br>
                        اتفاقية السرية وعدم الإفصاح<br>(NDA) للمحكمين الذين يعملون عن بعد
                    </div>
                    <div class="article-content-col">
                        <p class="sub-title">بند "السرية وحماية البيانات" للمحكمين المستقلين :</p>
                        <ol>
                            <li>
                                <strong>(1) السرية وحماية خصوصية البيانات ( Confidentiality &amp; Data Protection )</strong><br>
                                <ul>
                                    <li>تعريف المعلومات السرية : تشمل المعلومات السرية كل ما يطلع عليه المحكم أثناء عمله، بما في ذلك (بيانات المستخدمين الشخصية، صور المنتجات، فواتير الشراء، تقارير الفحص الفني، آليات التسعير الخاصة بالتطبيق، أو أي معلومات برمجية أو تقنية داخل المنصة ).</li>
                                </ul>
                            </li>
                            <li>(2) حظر الاستخدام الشخصي: يلتزم المحكم بعدم استخدام أي معلومة حصل عليها بطريقة مباشرة لمنفعته الشخصية أو لمنفعة أطراف ثالثة . كما يُحظر عليه التواصل مع أصحاب المنتجات مباشرة خارج إطار التطبيق لأي سبب كان.</li>
                            <li>(3) حظر النسخ والتخزين: يُمنع المحكم منعاً باتاً من أخذ لقطات شاشة (Screenshots) لبيانات المستخدمين أو تحميل وحفظ صور المنتجات والتقارير في أجهزته الشخصية إلا للضرورة القصوى التي يقتضيها التحكيم، ويلتزم بحذفها فور انتهاء العملية.</li>
                            <li>(4) أمن الضرورة: المحكم مسؤول مسؤولية كاملة عن سرية بيانات دخوله، وأنه لن يشاركها مع أي جهة.</li>
                            <li>(5) مدة الالتزام: يظل هذا البند سارياً طوال مدة تعاقده مع التطبيق، وبعده (3 سنوات) بعد إنهاء العلاقة التعاقدية لأي سبب كان.</li>
                            <li>(6) التعويض عن الإفصاح: في حال ثبت تسريب، المحكم ملزم بدفع تعويض عن سرية معلومة سرية يُقدر بـ ( 10,000 ريال )، بالإضافة إلى حق التطبيق في المطالبة بالتعويض عن الأضرار الفعلية.</li>
                        </ol>
                    </div>
                </div>

                {{-- Article 2 --}}
                <div class="article-row">
                    <div class="article-label-col">
                        <span class="art-num">2</span><br>
                        بنود الشروط العامة
                    </div>
                    <div class="article-content-col">
                        <p>تأسيس علاقة مهنية مع محكمين بنظام العمل الحر ( Freelance ) يتطلب بنوداً تجمع بين الدقة التقنية والالتزام القانوني :</p>
                        <ol>
                            <li><strong>(1) معايير التقييم والنزاهة (Technical Integrity):</strong>
                                <ul>
                                    <li>الالتزام بالدليل الإرشادي: يجب أن يعتمد المحكم دائماً الدليل الإرشادي الصادر عن إدارة التطبيق.</li>
                                    <li>دقة البيانات: المحكم مسؤول عن مراجعة الصور والمستندات المرفوعة والتأكد من مطابقتها للإثبات التقنية المطلوبة.</li>
                                    <li>الحيادية: يُمنع التلاعب بالتثمين لرفع أو خفض قيمة المنتج لأسباب غير موضوعية.</li>
                                </ul>
                            </li>
                            <li><strong>(2) نطاق العمل والمسؤولية (Scope of Work):</strong>
                                <ul>
                                    <li>زمن الاستجابة (SLA): تحديد مهلة مضمونة لكل عملية تحكيم (مثلاً: اكتمال الطلب خلال 30 دقيقة من وصول الطلب).</li>
                                    <li>التوثيق: يصدر قرار المحكم توثيقياً في حال ثبت وجود خطأ جسيم أو مخالفة للمعايير التقنية.</li>
                                </ul>
                            </li>
                            <li><strong>(3) السرية وخصوصية البيانات (Confidentiality):</strong>
                                <ul>
                                    <li>حماية البيانات: يحظر على المحكم الاحتفاظ بصور المنتجات أو بيانات المستخدمين أو استخدامها خارج إطار التطبيق.</li>
                                    <li>الملكية الفكرية: جميع التقارير والنتائج التي يصدرها المحكم هي ملك حصري للتطبيق.</li>
                                </ul>
                            </li>
                            <li><strong>(4) آلية المحاسبة المالية (Payment Terms):</strong>
                                <ul>
                                    <li>نظام العمولة: تحديد أجر معلوم مقابل كل عملية تحكيم ناجحة ومكتملة.</li>
                                    <li>الخصومات: يحق للتطبيق خصم قيمة العمولة أو إلغائها في حال ثبت طعن المحتكمين أو تكرار الأخطاء.</li>
                                </ul>
                            </li>
                            <li><strong>(5) بند "العمل الحر" والقانون (Legal Status):</strong>
                                <ul>
                                    <li>استقلالية المحكم: الأكيد على أن العلاقة مع المنصة هي "تعاقد مستقل" وليست علاقة توظيف.</li>
                                    <li>عدم المنافسة: يُمنع المحكم من العمل مع تطبيقات مباشرة منافسة خلال فترة التعاقد.</li>
                                </ul>
                            </li>
                            <li><strong>(6) إنهاء التعاقد (Termination):</strong> يحق للتطبيق إيقاف حساب المحكم فوراً في حالات: (إفشاء الأسرار، التواطؤ، التأخير المكرر، أو ثبوت الغش والاحتيال بناءً على تقييمات المستخدمين).</li>
                        </ol>
                    </div>
                </div>

                {{-- Article 3 --}}
                <div class="article-row">
                    <div class="article-label-col">
                        <span class="art-num">3</span><br>
                        آلية العمل والمستحقات
                    </div>
                    <div class="article-content-col">
                        <p>يقوم المحكم بالدخول على رابط الانضمام والإقرار بالوثيقة وبعد ذلك يتم تمكينه من استقبال عروض التثمين ومراجعتها. يُقيّم العرض وفق الآلية التثمينية للمنتج بتقدير ثلاث قيم ( الحد الأدنى / السعر العادل / الحد الأعلى ). ويحصل إزاء كل بطاقة ثمين بهامش عمولة تقدر بـ ( SR 10 ) عن كل بطاقة ثمين منجزة بنجاح بعد مرور ( 24 - 48 ساعة ) من عملية التثمين وذلك بتحويلها إلى حسابه المدون في صفحته وفق آلية التطبيق.</p>
                    </div>
                </div>

                {{-- Article 4 --}}
                <div class="article-row">
                    <div class="article-label-col">
                        <span class="art-num">4</span><br>
                        المسؤولية المهنية والتعويض
                    </div>
                    <div class="article-content-col">
                        <ol>
                            <li>(1) دقة التقييم : يُقر "المحكم" بأن كافة التقارير والنتائج الصادرة عنه مبنية على أسس مهنية وموضوعية ، وأنه مسؤول مسؤولية كاملة عن مراجعة كافة الصور والمستندات المرفقة بالطلب بدقة وعناية فائقة .</li>
                            <li>(2) حدود المسؤولية : لا يتحمل المحكم المسؤولية عن العيوب "الخفية" التي لا يمكن كشفها من خلال الصور أو تقارير الفحص المرفقة، بشرط أن يثبت المحكم أنه بذل العناية المهنية اللازمة وفقاً للدليل الإرشادي للتطبيق .</li>
                            <li>(3) الخطأ المهني الجسيم : في حال ثبت وجود "خطأ مهني جسيم " ( مثل التغاضي عن كسر واضح في شاشة جوال ، أو إخفاء صدمة هيكلية في سيارة ، أو تزييف حالة البطارية ) أدى إلى ثمين المنتج بسعر ( أعلى / أقل ) من قيمته الحقيقية بنسبة تتجاوز (10%) ، يحق لإدارة التطبيق اتخاذ الإجراءات التالية :
                                <ul>
                                    <li>تحميل المحكم قيمة الضرر المباشر للمستخدم بحد أقصى ( 3 أضعاف ) أتعابه في تلك العملية.</li>
                                    <li>التواصل والاحتيال: في حال ثبت تواطؤ بين المحكم وصاحب المنتج لرفع قيمة التقييم ، يتم إنهاء التعاقد فوراً دون سابق إنذار مع استحقاق التعويض القانوني.</li>
                                </ul>
                            </li>
                            <li>(4) آلية التظلم : يحق للمحكم الاعتراض على قرارات الخصم أو التقييمات خلال ( 3 أيام عمل ) من إخطاره ، وتقوم لجنة داخلية بالتطبيق بالفصل في الاعتراضات ويكون قرارها نهائياً .</li>
                        </ol>
                        <div class="how-box">
                            <div class="how-title">كيف يطبق هذا البند تقنياً داخل التطبيق ؟</div>
                            <ul>
                                <li>نظام تقييم التراكمي ( Accuracy Score ) : إذا بلغ تقييم المحكم في المنصة بمعدل دقة أقل من 90% يتم إيقافه للمراجعة.</li>
                                <li>تجميد الاستحقاقات: تجميد نصيب أتعاب المحكم خلال "فترة الاختبار" حتى يتم اعتماد الطلب بنجاح.</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>{{-- end articles --}}

            {{-- Read confirm --}}
            <label class="read-confirm" for="readConfirm">
                <input type="checkbox" id="readConfirm" onchange="updateProgress()">
                <label for="readConfirm">نعم أقر بالاتفاقية — لقد اطلعت على جميع بنود الوثيقة وأوافق على الالتزام بها كاملاً</label>
            </label>

            {{-- Signature --}}
            <div class="section-lbl">التوقيع الرقمي</div>

            <div class="sig-wrap" id="sigContainer">
                <canvas id="signatureCanvas"></canvas>
                <div class="sig-bar">
                    <span class="sig-hint">✏️ ارسم توقيعك بالماوس أو بإصبعك على الشاشة</span>
                    <button type="button" class="btn-clear-sig" onclick="clearSignature()">🗑 مسح</button>
                </div>
            </div>
            <span class="sig-err" id="err_sig">⚠️ يرجى رسم توقيعك قبل الإرسال</span>
            <input type="hidden" name="signature" id="sigInput">

            {{-- Date + submit --}}
            <div class="date-row">
                <div>
                    <strong>التاريخ:</strong>
                    <span class="date-val">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div style="font-size:11px; color:#bbb;">
                    رقم الوثيقة: {{ strtoupper(substr($declaration->token, 0, 12)) }}
                </div>
            </div>

            <div class="submit-wrap">
                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    <span class="spinner" id="spinner"></span>
                    <span id="btnText">✅ أقر وأوافق وأوقع على الوثيقة</span>
                </button>
                <p class="submit-note">ستُحفظ نسخة PDF موقعة يمكن تحميلها في أي وقت</p>
            </div>

        </form>
        @endif

    </div>{{-- doc-content --}}

    <div class="doc-footer">
        هذه الوثيقة صادرة رسمياً من <strong>منصة ثمن</strong> للتثمين المهني — جميع الحقوق محفوظة © {{ date('Y') }}
    </div>

</div>{{-- doc-shell --}}

<script>
const canvas = document.getElementById('signatureCanvas');
if (canvas) {
    const ctx = canvas.getContext('2d');
    let drawing = false, hasSig = false;

    function resize() {
        const dpr = window.devicePixelRatio || 1;
        const w = canvas.parentElement.getBoundingClientRect().width;
        canvas.width = w * dpr;
        canvas.height = 150 * dpr;
        canvas.style.height = '150px';
        ctx.scale(dpr, dpr);
        ctx.strokeStyle = '#1a1a2e';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }
    resize();
    window.addEventListener('resize', resize);

    const pos = e => {
        const r = canvas.getBoundingClientRect();
        const s = e.touches ? e.touches[0] : e;
        return { x: s.clientX - r.left, y: s.clientY - r.top };
    };

    canvas.addEventListener('mousedown', e => { drawing = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasSig = true; updateProgress(); });
    canvas.addEventListener('mouseup', () => { drawing = false; saveSig(); });
    canvas.addEventListener('mouseleave', () => { drawing = false; });
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, { passive: false });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasSig = true; updateProgress(); }, { passive: false });
    canvas.addEventListener('touchend', () => { drawing = false; saveSig(); });

    function saveSig() {
        if (hasSig) document.getElementById('sigInput').value = canvas.toDataURL('image/png');
    }

    window.clearSignature = () => {
        const dpr = window.devicePixelRatio || 1;
        ctx.clearRect(0, 0, canvas.width / dpr, canvas.height / dpr);
        hasSig = false;
        document.getElementById('sigInput').value = '';
        updateProgress();
    };

    window.updateProgress = function () {
        const ok = {
            name:  (document.getElementById('full_name')?.value || '').trim().length > 0,
            nid:   (document.getElementById('national_id')?.value || '').trim().length >= 10,
            phone: (document.getElementById('phone')?.value || '').trim().length > 0,
            email: (document.getElementById('email')?.value || '').trim().length > 0,
            read:  document.getElementById('readConfirm')?.checked,
            sig:   hasSig
        };
        const done = Object.values(ok).filter(Boolean).length;
        const fill = document.getElementById('progressFill');
        if (fill) fill.style.width = (done / 6 * 100) + '%';
        const btn = document.getElementById('submitBtn');
        if (btn) btn.disabled = !Object.values(ok).every(Boolean);
    };

    ['full_name','national_id','phone','email'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', updateProgress);
    });

    document.getElementById('declarationForm')?.addEventListener('submit', function(e) {
        if (!hasSig) {
            e.preventDefault();
            const err = document.getElementById('err_sig');
            err.style.display = 'block';
            document.getElementById('sigContainer').style.borderColor = '#dc2626';
            canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        saveSig();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        document.getElementById('spinner').style.display = 'inline-block';
        document.getElementById('btnText').textContent = 'جارٍ الحفظ...';
    });

    updateProgress();
}
</script>

</body>
</html>
