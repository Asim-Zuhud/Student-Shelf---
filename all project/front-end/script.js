const API_BASE = 'http://localhost/university-books/back-end/api';

// دالة مساعدة للاتصال بالـ API
async function apiCall(endpoint, options = {}) {
    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`خطأ في الخادم: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('خطأ في الاتصال:', error);
        throw error;
    }
}

// دالة لتحميل الكليات من الـ API
async function loadColleges() {
    try {
        return await apiCall('colleges.php');
    } catch (error) {
        showError('خطأ في تحميل الكليات');
        return [];
    }
}

// دالة لتحميل منشورات كلية معينة
async function loadPosts(collegeId) {
    try {
        return await apiCall(`posts.php?college_id=${collegeId}`);
    } catch (error) {
        showError('خطأ في تحميل المنشورات');
        return [];
    }
}

// دالة لعرض الكليات
async function renderColleges() {
    const container = document.querySelector(".college-list");
    if (!container) return;

    container.innerHTML = '<div class="loading">جاري تحميل الكليات...</div>';

    const colleges = await loadColleges();
    container.innerHTML = "";

    colleges.forEach(college => {
        const a = document.createElement("a");
        a.href = `college.html?college_id=${college.id}&college_name=${encodeURIComponent(college.name)}`;
        a.textContent = college.name;
        container.appendChild(a);
    });
}

// دالة لعرض منشورات الكلية
async function renderPosts(collegeId) {
    const container = document.querySelector(".posts");
    if (!container) return;

    container.innerHTML = '<div class="loading">جاري تحميل الكتب...</div>';

    const posts = await loadPosts(collegeId);
    container.innerHTML = "";

    if (posts.length === 0) {
        container.innerHTML = '<div class="error" style="grid-column: 1/-1; text-align: center; padding: 2rem;">لا توجد كتب معروضة في هذه الكلية حالياً</div>';
        return;
    }

    posts.forEach(post => {
        const postCard = document.createElement("div");
        postCard.className = "post-card";
        
        const priceHTML = post.price ? `<p><strong>السعر:</strong> ${post.price} ₪</p>` : '';
        const descriptionHTML = post.description ? `<p><strong>الوصف:</strong> ${post.description}</p>` : '';
        
        postCard.innerHTML = `
            <img src="${post.image_url || 'https://via.placeholder.com/150x180/003366/ffffff?text=📚'}" alt="صورة ${post.title}">
            <h3>${post.title}</h3>
            <p><strong>النوع:</strong> ${post.book_type}</p>
            ${priceHTML}
            <p><strong>الناشر:</strong> ${post.user_name}</p>
            <p><strong>للتواصل:</strong> ${post.contact_info}</p>
            ${descriptionHTML}
            <p><strong>تاريخ النشر:</strong> ${new Date(post.created_at).toLocaleDateString('ar-EG')}</p>
        `;
        
        container.appendChild(postCard);
    });
}

// إعداد صفحة الكلية
async function setupCollegePage() {
    const params = new URLSearchParams(window.location.search);
    const collegeId = params.get('college_id');
    const collegeName = params.get('college_name');

    if (collegeName) {
        const headerEl = document.querySelector("header h1");
        if (headerEl) {
            headerEl.textContent = decodeURIComponent(collegeName);
        }
    }

    if (collegeId) {
        await renderPosts(collegeId);
        
        // إضافة حدث لزر إضافة منشور
        const addPostBtn = document.querySelector('.add-post');
        if (addPostBtn) {
            addPostBtn.addEventListener('click', () => {
                showAddPostModal(collegeId);
            });
        }
    } else {
        showError('لم يتم تحديد الكلية');
    }
}

// عرض نموذج إضافة منشور
function showAddPostModal(collegeId) {
    const modalHTML = `
        <div class="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
            <div style="background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 500px;">
                <h3>إضافة كتاب جديد</h3>
                <p style="color: #666; margin-bottom: 1rem;">هذه خاصية تجريبية - تحتاج تطوير نظام المستخدمين أولاً</p>
                <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #003366; color: white; border: none; border-radius: 5px; cursor: pointer;">حسناً</button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// عرض رسالة خطأ
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error';
    errorDiv.textContent = message;
    
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(errorDiv, main.firstChild);
    }
}

// التنفيذ عند تحميل الصفحة
document.addEventListener("DOMContentLoaded", async () => {
    if (document.querySelector(".college-list")) {
        await renderColleges();
    }

    if (document.querySelector(".posts")) {
        await setupCollegePage();
    }
});