<?php require 'config.php'; ?>
<?php include 'header.php'; ?>

<div class="container reviews-page">
    <h1><i class="fas fa-star"></i> Отзывы наших пациентов</h1>
    
    <div class="reviews-layout">
        <!-- Левая колонка - статистика -->
        <div class="reviews-sidebar">
            <div class="stats-card">
                <div class="avg-rating">
                    <div class="rating-number"><?= getAverageRating($pdo) ?></div>
                    <div class="rating-stars" id="avgStars"></div>
                    <div class="rating-count">на основе <?= getTotalReviews($pdo) ?> отзывов</div>
                </div>
                
                <div class="rating-breakdown" id="ratingBreakdown"></div>
                
                <button class="btn-primary write-review-btn" id="writeReviewBtn">
                    <i class="fas fa-pen"></i> Оставить отзыв
                </button>
            </div>
        </div>
        
        <!-- Правая колонка - список отзывов -->
        <div class="reviews-main">
            <div class="reviews-filters">
                <div class="filter-group">
                    <label>Оценка:</label>
                    <select id="filterRating">
                        <option value="all">Все оценки</option>
                        <option value="5">★ 5 звёзд</option>
                        <option value="4">★★★★ 4+</option>
                        <option value="3">★★★ 3+</option>
                        <option value="2">★★ 2+</option>
                        <option value="1">★ 1+</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Сортировка:</label>
                    <select id="filterSort">
                        <option value="newest">Сначала новые</option>
                        <option value="oldest">Сначала старые</option>
                        <option value="highest">С высоким рейтингом</option>
                        <option value="lowest">С низким рейтингом</option>
                    </select>
                </div>
            </div>
            
            <div id="reviewsList" class="reviews-list">
                <div class="loading">Загрузка отзывов...</div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для добавления отзыва -->
<div id="reviewModal" class="modal">
    <div class="modal-content review-modal">
        <span class="close" onclick="closeReviewModal()">&times;</span>
        <h2><i class="fas fa-star"></i> Оставить отзыв</h2>
        
        <div id="reviewCheckResult"></div>
        
        <form id="reviewForm" style="display:none;">
            <div class="form-group">
                <label>Ваша оценка *</label>
                <div class="rating-input">
                    <span class="star-input" data-value="1">★</span>
                    <span class="star-input" data-value="2">★</span>
                    <span class="star-input" data-value="3">★</span>
                    <span class="star-input" data-value="4">★</span>
                    <span class="star-input" data-value="5">★</span>
                </div>
                <input type="hidden" id="reviewRating" required>
            </div>
            
            <div class="form-group">
                <label>Достоинства (что понравилось?)</label>
                <textarea id="reviewAdvantages" rows="2" placeholder="Например: профессионализм врачей, чистота, вежливый персонал..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Недостатки (что можно улучшить?)</label>
                <textarea id="reviewDisadvantages" rows="2" placeholder="Будем благодарны за конструктивную критику..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Ваш отзыв *</label>
                <textarea id="reviewComment" rows="4" required placeholder="Расскажите о вашем опыте посещения клиники..."></textarea>
            </div>
            
            <button type="submit" class="btn-primary">Отправить отзыв</button>
        </form>
        
        <div id="reviewSuccessMessage" style="display:none; text-align:center; padding:30px;">
            <i class="fas fa-check-circle" style="font-size:64px; color:#10b981;"></i>
            <h3>Спасибо за ваш отзыв!</h3>
            <p>Он будет опубликован после проверки модератором.</p>
            <button onclick="closeReviewModal()" class="btn-primary" style="margin-top:20px;">Закрыть</button>
        </div>
    </div>
</div>

<style>
.reviews-page {
    padding: 40px 20px;
}

.reviews-page h1 {
    text-align: center;
    margin-bottom: 40px;
    color: #1e3a3f;
}

.reviews-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 40px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Левая панель */
.reviews-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.stats-card {
    background: white;
    border-radius: 28px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.avg-rating {
    margin-bottom: 30px;
}

.rating-number {
    font-size: 64px;
    font-weight: 800;
    color: #0F766E;
    line-height: 1;
}

.rating-stars {
    font-size: 24px;
    color: #fbbf24;
    margin: 10px 0;
}

.rating-count {
    color: #6b7280;
    font-size: 14px;
}

.rating-breakdown {
    margin: 20px 0;
}

.rating-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.rating-row span:first-child {
    width: 50px;
    font-size: 14px;
}

.rating-bar {
    flex: 1;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.rating-bar-fill {
    height: 100%;
    background: #fbbf24;
    border-radius: 4px;
    transition: width 0.3s;
}

.rating-row span:last-child {
    width: 40px;
    font-size: 14px;
    color: #6b7280;
}

.write-review-btn {
    width: 100%;
    margin-top: 20px;
    padding: 14px;
    font-size: 1rem;
}

/* Правая панель */
.reviews-filters {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    margin: 0;
    font-weight: 500;
}

.filter-group select {
    width: auto;
    padding: 8px 16px;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.review-card {
    background: white;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}

.review-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 10px;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.reviewer-avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #0F766E, #1BAF8C);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 18px;
}

.reviewer-name {
    font-weight: 600;
    font-size: 1.1rem;
}

.review-date {
    color: #9ca3af;
    font-size: 0.85rem;
}

.review-stars {
    color: #fbbf24;
    font-size: 18px;
    letter-spacing: 2px;
}

.review-text {
    margin: 16px 0;
    line-height: 1.6;
    color: #374151;
}

.review-pros-cons {
    display: flex;
    gap: 20px;
    margin: 16px 0;
    flex-wrap: wrap;
}

.review-pros, .review-cons {
    flex: 1;
    padding: 12px;
    border-radius: 16px;
    font-size: 0.9rem;
}

.review-pros {
    background: #ecfdf5;
    border-left: 3px solid #10b981;
}

.review-cons {
    background: #fef2f2;
    border-left: 3px solid #ef4444;
}

.review-pros strong, .review-cons strong {
    display: block;
    margin-bottom: 8px;
}

.admin-response {
    background: #f0fdf4;
    border-radius: 16px;
    padding: 16px;
    margin-top: 16px;
    border-left: 3px solid #0F766E;
}

.admin-response strong {
    color: #0F766E;
    display: block;
    margin-bottom: 8px;
}

.loading, .no-reviews {
    text-align: center;
    padding: 60px;
    color: #6b7280;
}

/* Модалка отзыва */
.review-modal {
    max-width: 600px;
}

.rating-input {
    display: flex;
    gap: 10px;
    font-size: 32px;
    cursor: pointer;
}

.star-input {
    color: #d1d5db;
    transition: color 0.2s;
    cursor: pointer;
}

.star-input:hover,
.star-input.active {
    color: #fbbf24;
}

/* Адаптив */
@media (max-width: 768px) {
    .reviews-layout {
        grid-template-columns: 1fr;
    }
    
    .reviews-sidebar {
        position: static;
    }
    
    .review-pros-cons {
        flex-direction: column;
    }
    
    .reviews-filters {
        flex-direction: column;
    }
    
    .filter-group select {
        width: 100%;
    }
}
</style>

<script>
let currentReviews = [];

// Получение средней оценки
function getAverageRating() {
    const stats = window.reviewsStats || { avg_rating: 0, total: 0 };
    return stats.avg_rating || 0;
}

// Загрузка отзывов
async function loadReviews() {
    const rating = document.getElementById('filterRating').value;
    const sort = document.getElementById('filterSort').value;
    
    try {
        const response = await fetch(`api.php?action=get_reviews&status=approved&rating=${rating}&sort=${sort}`);
        const data = await response.json();
        
        currentReviews = data.reviews;
        window.reviewsStats = data.stats;
        
        renderReviews(data.reviews);
        renderStats(data.stats);
    } catch (error) {
        console.error('Ошибка загрузки отзывов:', error);
        document.getElementById('reviewsList').innerHTML = '<div class="no-reviews">Ошибка загрузки отзывов</div>';
    }
}

// Отображение отзывов
function renderReviews(reviews) {
    const container = document.getElementById('reviewsList');
    
    if (!reviews || reviews.length === 0) {
        container.innerHTML = '<div class="no-reviews"><i class="fas fa-comment-dots"></i><br>Пока нет отзывов. Будьте первым!</div>';
        return;
    }
    
    container.innerHTML = reviews.map(review => {
        const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
        const date = new Date(review.created_at).toLocaleDateString('ru-RU');
        const initials = (review.user_name || 'Пользователь').charAt(0).toUpperCase();
        
        return `
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">${initials}</div>
                        <div>
                            <div class="reviewer-name">${escapeHtml(review.user_name || 'Пользователь')}</div>
                            <div class="review-date">${date}</div>
                        </div>
                    </div>
                    <div class="review-stars">${stars}</div>
                </div>
                
                ${review.advantages ? `
                    <div class="review-pros">
                        <strong><i class="fas fa-thumbs-up"></i> Достоинства:</strong>
                        ${escapeHtml(review.advantages)}
                    </div>
                ` : ''}
                
                ${review.disadvantages ? `
                    <div class="review-cons">
                        <strong><i class="fas fa-thumbs-down"></i> Недостатки:</strong>
                        ${escapeHtml(review.disadvantages)}
                    </div>
                ` : ''}
                
                <div class="review-text">${escapeHtml(review.comment)}</div>
                
                ${review.admin_response ? `
                    <div class="admin-response">
                        <strong><i class="fas fa-comment-dots"></i> Ответ администрации:</strong>
                        ${escapeHtml(review.admin_response)}
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

// Отображение статистики
function renderStats(stats) {
    if (!stats) return;
    
    // Обновляем среднюю оценку
    const avgNumber = document.querySelector('.rating-number');
    if (avgNumber) avgNumber.textContent = stats.avg_rating.toFixed(1);
    
    const avgStars = document.getElementById('avgStars');
    if (avgStars) {
        const fullStars = Math.floor(stats.avg_rating);
        const halfStar = stats.avg_rating % 1 >= 0.5;
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) starsHtml += '★';
            else if (i === fullStars + 1 && halfStar) starsHtml += '½';
            else starsHtml += '☆';
        }
        avgStars.innerHTML = starsHtml;
    }
    
    const ratingCount = document.querySelector('.rating-count');
    if (ratingCount) ratingCount.textContent = `на основе ${stats.total} отзывов`;
    
    // Рендерим breakdown
    const breakdown = document.getElementById('ratingBreakdown');
    if (breakdown) {
        const total = stats.total || 1;
        breakdown.innerHTML = `
            <div class="rating-row">
                <span>5 ★</span>
                <div class="rating-bar"><div class="rating-bar-fill" style="width: ${(stats['5_stars'] / total) * 100}%"></div></div>
                <span>${stats['5_stars']}</span>
            </div>
            <div class="rating-row">
                <span>4 ★</span>
                <div class="rating-bar"><div class="rating-bar-fill" style="width: ${(stats['4_stars'] / total) * 100}%"></div></div>
                <span>${stats['4_stars']}</span>
            </div>
            <div class="rating-row">
                <span>3 ★</span>
                <div class="rating-bar"><div class="rating-bar-fill" style="width: ${(stats['3_stars'] / total) * 100}%"></div></div>
                <span>${stats['3_stars']}</span>
            </div>
            <div class="rating-row">
                <span>2 ★</span>
                <div class="rating-bar"><div class="rating-bar-fill" style="width: ${(stats['2_stars'] / total) * 100}%"></div></div>
                <span>${stats['2_stars']}</span>
            </div>
            <div class="rating-row">
                <span>1 ★</span>
                <div class="rating-bar"><div class="rating-bar-fill" style="width: ${(stats['1_stars'] / total) * 100}%"></div></div>
                <span>${stats['1_stars']}</span>
            </div>
        `;
    }
}

// Модальное окно для отзыва
async function openReviewModal() {
    const modal = document.getElementById('reviewModal');
    const checkDiv = document.getElementById('reviewCheckResult');
    const form = document.getElementById('reviewForm');
    const successDiv = document.getElementById('reviewSuccessMessage');
    
    modal.style.display = 'block';
    form.style.display = 'none';
    successDiv.style.display = 'none';
    
    try {
        const response = await fetch('api.php?action=check_can_review');
        const data = await response.json();
        
        if (data.can_review) {
            checkDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Вы можете оставить отзыв</div>';
            form.style.display = 'block';
            resetReviewForm();
        } else {
            checkDiv.innerHTML = `<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ${data.message}</div>`;
        }
    } catch (error) {
        checkDiv.innerHTML = '<div class="alert alert-error">Ошибка проверки</div>';
    }
}

function resetReviewForm() {
    document.getElementById('reviewRating').value = '';
    document.querySelectorAll('.star-input').forEach(s => s.classList.remove('active'));
    document.getElementById('reviewAdvantages').value = '';
    document.getElementById('reviewDisadvantages').value = '';
    document.getElementById('reviewComment').value = '';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

// Рейтинг в форме
document.addEventListener('DOMContentLoaded', () => {
    const starInputs = document.querySelectorAll('.star-input');
    starInputs.forEach(star => {
        star.addEventListener('click', () => {
            const value = parseInt(star.dataset.value);
            document.getElementById('reviewRating').value = value;
            starInputs.forEach(s => {
                if (parseInt(s.dataset.value) <= value) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
});

// Отправка отзыва
document.getElementById('reviewForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const rating = document.getElementById('reviewRating').value;
    const comment = document.getElementById('reviewComment').value;
    
    if (!rating) {
        alert('Поставьте оценку');
        return;
    }
    
    if (!comment.trim()) {
        alert('Напишите текст отзыва');
        return;
    }
    
    const data = {
        rating: parseInt(rating),
        advantages: document.getElementById('reviewAdvantages').value,
        disadvantages: document.getElementById('reviewDisadvantages').value,
        comment: comment
    };
    
    try {
        const response = await fetch('api.php?action=add_review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('reviewForm').style.display = 'none';
            document.getElementById('reviewSuccessMessage').style.display = 'block';
            // Перезагружаем отзывы через 2 секунды
            setTimeout(() => {
                loadReviews();
            }, 2000);
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('Ошибка отправки отзыва');
    }
});

// Фильтры
document.getElementById('filterRating')?.addEventListener('change', loadReviews);
document.getElementById('filterSort')?.addEventListener('change', loadReviews);
document.getElementById('writeReviewBtn')?.addEventListener('click', openReviewModal);

// Загружаем отзывы
loadReviews();

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>

<?php
function getAverageRating($pdo) {
    $stmt = $pdo->query("SELECT AVG(rating) as avg FROM review WHERE status = 'approved'");
    $result = $stmt->fetch();
    return round($result['avg'] ?? 0, 1);
}

function getTotalReviews($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM review WHERE status = 'approved'");
    return $stmt->fetchColumn();
}
?>

<?php include 'footer.php'; ?>