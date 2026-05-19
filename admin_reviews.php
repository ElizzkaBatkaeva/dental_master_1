<?php 
require 'config.php';
if(!isAdmin()) { header('Location: index.php'); exit; }
include 'header.php';
?>

<div class="container">
    <h1><i class="fas fa-star"></i> Управление отзывами</h1>
    
    <div id="adminReviewsContainer">
        <div class="loading">Загрузка отзывов...</div>
    </div>
</div>

<div id="responseModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeResponseModal()">&times;</span>
        <h3>Ответ на отзыв</h3>
        <textarea id="responseText" rows="4" style="width:100%; margin:15px 0;" placeholder="Введите ответ на отзыв..."></textarea>
        <button onclick="submitResponse()" class="btn-primary">Отправить ответ</button>
    </div>
</div>

<style>
.review-item {
    background: white;
    border-radius: 24px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.review-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-pending {
    background: #fef3c7;
    color: #b45309;
}

.badge-approved {
    background: #d1fae5;
    color: #065f46;
}

.badge-rejected {
    background: #fee2e2;
    color: #b91c1c;
}

.review-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 16px;
    font-size: 0.85rem;
}

.btn-response {
    background: #0F766E;
    color: white;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-delete {
    background: #6b7280;
    color: white;
}
</style>

<script>
let currentReviewId = null;

async function loadAdminReviews() {
    try {
        const response = await fetch('api.php?action=admin_reviews');
        const reviews = await response.json();
        
        const container = document.getElementById('adminReviewsContainer');
        
        if (!reviews || reviews.length === 0) {
            container.innerHTML = '<div class="no-reviews">Нет отзывов для модерации</div>';
            return;
        }
        
        container.innerHTML = reviews.map(review => {
            const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
            const date = new Date(review.created_at).toLocaleDateString('ru-RU');
            const statusText = review.status === 'pending' ? 'На модерации' : (review.status === 'approved' ? 'Одобрен' : 'Отклонён');
            const statusClass = review.status === 'pending' ? 'badge-pending' : (review.status === 'approved' ? 'badge-approved' : 'badge-rejected');
            
            return `
                <div class="review-item" data-id="${review.id}">
                    <div class="review-header">
                        <div>
                            <strong>${escapeHtml(review.user_name)}</strong> (${escapeHtml(review.user_email)})
                            <div class="review-stars" style="color: #fbbf24; margin-top: 5px;">${stars}</div>
                        </div>
                        <div>
                            <span class="review-badge ${statusClass}">${statusText}</span>
                            <div style="font-size: 0.85rem; color: #9ca3af; margin-top: 5px;">${date}</div>
                        </div>
                    </div>
                    
                    ${review.advantages ? `
                        <div style="background: #ecfdf5; padding: 12px; border-radius: 12px; margin: 12px 0;">
                            <strong>✅ Достоинства:</strong> ${escapeHtml(review.advantages)}
                        </div>
                    ` : ''}
                    
                    ${review.disadvantages ? `
                        <div style="background: #fef2f2; padding: 12px; border-radius: 12px; margin: 12px 0;">
                            <strong>⚠️ Недостатки:</strong> ${escapeHtml(review.disadvantages)}
                        </div>
                    ` : ''}
                    
                    <div style="margin: 12px 0; line-height: 1.6;">
                        <strong>📝 Отзыв:</strong><br>${escapeHtml(review.comment)}
                    </div>
                    
                    ${review.admin_response ? `
                        <div style="background: #f0fdf4; padding: 12px; border-radius: 12px; margin: 12px 0; border-left: 3px solid #0F766E;">
                            <strong>💬 Ответ администрации:</strong> ${escapeHtml(review.admin_response)}
                        </div>
                    ` : ''}
                    
                    <div class="review-actions">
                        ${review.status === 'pending' ? `
                            <button class="btn-sm btn-approve" onclick="moderateReview(${review.id}, 'approved')">✓ Одобрить</button>
                            <button class="btn-sm btn-reject" onclick="moderateReview(${review.id}, 'rejected')">✗ Отклонить</button>
                        ` : ''}
                        <button class="btn-sm btn-response" onclick="openResponseModal(${review.id})">💬 Ответить</button>
                        <button class="btn-sm btn-delete" onclick="deleteReview(${review.id})">🗑 Удалить</button>
                    </div>
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Ошибка загрузки:', error);
        document.getElementById('adminReviewsContainer').innerHTML = '<div class="no-reviews">Ошибка загрузки отзывов</div>';
    }
}

async function moderateReview(reviewId, status) {
    if (!confirm(`Вы уверены, что хотите ${status === 'approved' ? 'одобрить' : 'отклонить'} этот отзыв?`)) return;
    
    try {
        const response = await fetch('api.php?action=moderate_review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ review_id: reviewId, status: status })
        });
        
        const result = await response.json();
        if (result.success) {
            loadAdminReviews();
        } else {
            alert('Ошибка: ' + result.message);
        }
    } catch (error) {
        alert('Ошибка при модерации');
    }
}

function openResponseModal(reviewId) {
    currentReviewId = reviewId;
    document.getElementById('responseModal').style.display = 'block';
    document.getElementById('responseText').value = '';
}

function closeResponseModal() {
    document.getElementById('responseModal').style.display = 'none';
    currentReviewId = null;
}

async function submitResponse() {
    const responseText = document.getElementById('responseText').value;
    if (!responseText.trim()) {
        alert('Введите текст ответа');
        return;
    }
    
    try {
        const response = await fetch('api.php?action=moderate_review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                review_id: currentReviewId, 
                status: 'approved',
                admin_response: responseText 
            })
        });
        
        const result = await response.json();
        if (result.success) {
            closeResponseModal();
            loadAdminReviews();
        } else {
            alert('Ошибка: ' + result.message);
        }
    } catch (error) {
        alert('Ошибка при отправке ответа');
    }
}

async function deleteReview(reviewId) {
    if (!confirm('Удалить этот отзыв навсегда? Это действие нельзя отменить.')) return;
    
    try {
        const response = await fetch('api.php?action=delete_review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ review_id: reviewId })
        });
        
        const result = await response.json();
        if (result.success) {
            loadAdminReviews();
        } else {
            alert('Ошибка удаления');
        }
    } catch (error) {
        alert('Ошибка при удалении');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

window.onclick = function(e) {
    const modal = document.getElementById('responseModal');
    if (e.target === modal) closeResponseModal();
}

loadAdminReviews();
</script>

<?php include 'footer.php'; ?>