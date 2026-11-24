<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>تقييم خدمة التثمين | منصة ثمّن</title>
    <!-- Google Fonts & Bootstrap 5 & Boxicons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --thamn-gold: #c59b27;
            --thamn-gold-hover: #b0871d;
            --thamn-dark: #121417;
            --thamn-bg: #f8fafc;
        }

        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background-color: var(--thamn-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
        }

        .rating-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .rating-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 30px 20px 25px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .rating-header .brand-title {
            font-weight: 800;
            font-size: 1.5rem;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .rating-header .order-pill {
            display: inline-block;
            background: rgba(197, 155, 39, 0.18);
            color: #f7d57f;
            border: 1px solid rgba(197, 155, 39, 0.35);
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .rating-body {
            padding: 30px 25px;
        }

        .emoji-options {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin: 25px 0;
        }

        .emoji-btn {
            flex: 1;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .emoji-btn .icon {
            font-size: 2.2rem;
            line-height: 1;
            display: block;
            margin-bottom: 6px;
            transition: transform 0.2s ease;
        }

        .emoji-btn .score {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            display: block;
        }

        .emoji-btn .lbl {
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: 600;
            display: block;
            margin-top: 2px;
        }

        .emoji-btn:hover {
            border-color: var(--thamn-gold);
            background: #fffdf7;
            transform: translateY(-3px);
        }

        .emoji-btn:hover .icon {
            transform: scale(1.15);
        }

        .emoji-btn.active {
            border-color: var(--thamn-gold);
            background: rgba(197, 155, 39, 0.08);
            box-shadow: 0 4px 15px rgba(197, 155, 39, 0.2);
            transform: translateY(-4px);
        }

        .emoji-btn.active .score {
            color: var(--thamn-gold);
        }

        .emoji-btn.active .lbl {
            color: #b0871d;
            font-weight: 700;
        }

        .emoji-btn.active .icon {
            transform: scale(1.2);
        }

        .submit-btn {
            background: linear-gradient(135deg, #c59b27 0%, #b0871d 100%);
            border: none;
            color: white;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 14px;
            border-radius: 14px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(197, 155, 39, 0.3);
        }

        .submit-btn:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(197, 155, 39, 0.4);
        }

        .feedback-input {
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            padding: 12px 14px;
            font-size: 0.92rem;
            transition: border-color 0.2s ease;
        }

        .feedback-input:focus {
            border-color: var(--thamn-gold);
            box-shadow: 0 0 0 3px rgba(197, 155, 39, 0.15);
        }

        .info-pill {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="rating-card">
    <div class="rating-header">
        <div class="brand-title">ثَمِّن | Thmmn</div>
        <div class="order-pill">
            <i class="bx bx-receipt align-middle"></i> طلب رقم {{ $order->id }}
        </div>
        <p class="small text-white-50 mt-2 mb-0">نحرص دائماً على تقديم أعلى معايير الدقة والاحترافية</p>
    </div>

    <div class="rating-body">
        <div class="info-pill">
            <div>
                <span class="text-muted d-block small">القسم / التصنيف</span>
                <strong class="text-dark">{{ $order->category->name_ar ?? 'عام' }}</strong>
            </div>
            @if($order->expert)
                <div class="text-start">
                    <span class="text-muted d-block small">الخبير المعتمد</span>
                    <strong class="text-dark">{{ $order->expert->first_name . ' ' . $order->expert->last_name }}</strong>
                </div>
            @endif
        </div>

        <h5 class="text-center font-weight-bold mb-2 text-dark">
            كيف تقيّم تجربتك مع خدمة التثمين؟
        </h5>
        <p class="text-center text-muted small mb-0">اختر التقييم المناسب لتجربتك بضغطة واحدة:</p>

        <form id="ratingForm">
            @csrf
            <input type="hidden" name="rating" id="ratingInput" value="{{ $existingRating->rating ?? '' }}">

            <div class="emoji-options">
                <div class="emoji-btn {{ ($existingRating->rating ?? null) == 1 ? 'active' : '' }}" data-score="1">
                    <span class="icon">😡</span>
                    <span class="score">1</span>
                    <span class="lbl">سيئة جداً</span>
                </div>
                <div class="emoji-btn {{ ($existingRating->rating ?? null) == 2 ? 'active' : '' }}" data-score="2">
                    <span class="icon">🙁</span>
                    <span class="score">2</span>
                    <span class="lbl">سيئة</span>
                </div>
                <div class="emoji-btn {{ ($existingRating->rating ?? null) == 3 ? 'active' : '' }}" data-score="3">
                    <span class="icon">😐</span>
                    <span class="score">3</span>
                    <span class="lbl">متوسطة</span>
                </div>
                <div class="emoji-btn {{ ($existingRating->rating ?? null) == 4 ? 'active' : '' }}" data-score="4">
                    <span class="icon">🙂</span>
                    <span class="score">4</span>
                    <span class="lbl">جيدة</span>
                </div>
                <div class="emoji-btn {{ ($existingRating->rating ?? null) == 5 ? 'active' : '' }}" data-score="5">
                    <span class="icon">😍</span>
                    <span class="score">5</span>
                    <span class="lbl">ممتازة</span>
                </div>
            </div>

            <div class="mb-3" id="feedbackContainer" style="{{ ($existingRating->rating ?? null) ? 'display:block;' : 'display:none;' }}">
                <label for="feedback" class="form-label small text-muted font-weight-bold">
                    <i class="bx bx-message-square-dots text-warning"></i> ملاحظاتك أو مقترحاتك (اختياري)
                </label>
                <textarea class="form-control feedback-input" id="feedback" name="feedback" rows="3"
                          placeholder="هل لديك أي ملاحظات تود مشاركتها معنا لتطوير الخدمة؟">{{ $existingRating->feedback ?? '' }}</textarea>
            </div>

            <button type="submit" class="btn submit-btn" id="submitBtn">
                <i class="bx bx-check-circle align-middle"></i>
                <span id="btnText">{{ $existingRating ? 'تحديث التقييم' : 'إرسال التقييم' }}</span>
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bx bx-shield-quarter text-success align-middle"></i>
                تقييمك وملاحظاتك تساهم مباشرة في رفع جودة الخدمة وتكريم المتميزين.
            </small>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const emojiBtns = document.querySelectorAll('.emoji-btn');
        const ratingInput = document.getElementById('ratingInput');
        const feedbackContainer = document.getElementById('feedbackContainer');
        const form = document.getElementById('ratingForm');
        const submitBtn = document.getElementById('submitBtn');

        emojiBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                emojiBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const score = this.getAttribute('data-score');
                ratingInput.value = score;

                // Show feedback input with smooth transition
                feedbackContainer.style.display = 'block';
            });
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const score = ratingInput.value;
            if (!score) {
                Swal.fire({
                    icon: 'warning',
                    title: 'فضلاً اختر تقييمك أولاً',
                    text: 'اختر أحد الخيارات من 1 إلى 5 لتقييم تجربتك',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#c59b27'
                });
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الإرسال...';

            const formData = new FormData(form);

            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-check-circle"></i> تم إرسال التقييم';

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'شكراً جزيلاً لك! 🌟',
                        text: 'تم استلام تقييمك بنجاح، ونسعد دائماً بخدمتك في منصة ثمّن.',
                        confirmButtonText: 'إغلاق',
                        confirmButtonColor: '#c59b27'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'عذراً',
                        text: data.message || 'حدث خطأ أثناء حفظ التقييم',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-check-circle"></i> إرسال التقييم';
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: 'يرجى التأكد من اتصالك بالإنترنت والمحاولة مجدداً',
                    confirmButtonText: 'حسناً'
                });
            });
        });
    });
</script>

</body>
</html>
