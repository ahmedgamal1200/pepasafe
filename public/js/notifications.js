// --- Notification System ---

// --- الجزء الخاص بـ CSS (لا تغيير) ---
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
  }
  .notification-card.muted { opacity: 0.3; background-color: #f8f9fa; }
  .notification-card.muted .text-sm { color:rgb(156, 163, 175) !important; }
  .notification-card.muted .text-xs { color:rgb(187, 191, 196) !important; }
  .mute-indicator { position: absolute; top: 8px; right: 8px; background: #6b7280; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px; opacity: 0.8; }
  .notification-card.muted { border-left: 3px solid #6b7280; transition: all 0.3s ease; }
  .notification-card.muted:hover { background-color: #e5e7eb !important; transform: translateX(2px); }
  .mute-indicator { animation: pulse 2s infinite; }
  @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
  .notification-card[data-read="true"] { opacity: 0.4; background-color: #f8f9fa; border-left: 3px solid #d1d5db; }
  .notification-card[data-read="true"] .text-lg { color:rgb(156, 163, 175); }
  .notification-card[data-read="true"] .text-sm { color:rgb(156, 163, 175) !important; }
  .notification-card[data-read="true"] .text-xs { color:rgb(187, 191, 196) !important; }
`;
document.head.appendChild(style);

// --- Notification System ---
window.notificationSystem = {
    mutedNotifications: new Set(),
    notificationListElement: null,
    badgeElement: null,

    initializeNotifications() {
        this.notificationListElement = document.getElementById('notification-list');
        this.badgeElement = document.getElementById('notification-badge');

        if (!this.notificationListElement) {
            console.error('Element with ID "notification-list" not found.');
            return;
        }

        this.setupEventListeners();
        this.loadMutedState();
        this.updateUIAfterStateLoad();
    },

    setupEventListeners() {
        this.notificationListElement.addEventListener('click', (e) => {
            const card = e.target.closest('.notification-card');
            if (!card || e.target.closest('button') || e.target.closest('i')) {
                return;
            }
            this.markNotificationAsRead(card);
        });
    },

    updateUIAfterStateLoad() {
        this.notificationListElement.querySelectorAll('.notification-card').forEach(card => {
            if (this.mutedNotifications.has(card.dataset.id)) {
                card.classList.add('muted');
                this.addMuteIndicator(card);
            }
        });
        this.updateNotificationBadge();
    },

    markNotificationAsRead(notificationCard) {
        notificationCard.dataset.read = 'true';
        this.updateNotificationBadge();
    },

    toggleNotificationMute(notificationCard) {
        const notificationId = notificationCard.dataset.id;
        if (this.mutedNotifications.has(notificationId)) {
            this.mutedNotifications.delete(notificationId);
            notificationCard.classList.remove('muted');
            this.removeMuteIndicator(notificationCard);
        } else {
            this.mutedNotifications.add(notificationId);
            notificationCard.classList.add('muted');
            this.addMuteIndicator(notificationCard);
        }
        this.saveMutedState();
        this.updateNotificationBadge();
    },

    addMuteIndicator(notificationCard) {
        this.removeMuteIndicator(notificationCard);
        const indicator = document.createElement('div');
        indicator.className = 'mute-indicator';
        indicator.innerHTML = '<i class="fas fa-volume-mute"></i>';
        indicator.title = 'Notification muted';
        notificationCard.style.position = 'relative';
        notificationCard.appendChild(indicator);
    },

    removeMuteIndicator(notificationCard) {
        const existingIndicator = notificationCard.querySelector('.mute-indicator');
        if (existingIndicator) existingIndicator.remove();
    },

    saveMutedState() {
        try {
            localStorage.setItem('mutedNotifications', JSON.stringify(Array.from(this.mutedNotifications)));
        } catch (e) {
            console.warn('Could not save muted state:', e);
        }
    },

    loadMutedState() {
        try {
            const saved = localStorage.getItem('mutedNotifications');
            if (saved) this.mutedNotifications = new Set(JSON.parse(saved));
        } catch (e) {
            console.warn('Could not load muted state:', e);
        }
    },

    updateNotificationBadge() {
        if (!this.badgeElement) return;
        let activeUnreadCount = 0;
        this.notificationListElement.querySelectorAll('.notification-card[data-read="false"]').forEach(card => {
            if (!this.mutedNotifications.has(card.dataset.id)) {
                activeUnreadCount++;
            }
        });

        if (activeUnreadCount > 0) {
            this.badgeElement.textContent = activeUnreadCount;
            this.badgeElement.classList.remove('hidden');
        } else {
            this.badgeElement.classList.add('hidden');
        }
    },

    createNotificationElement(notification) {
        console.log("Creating new notification element. Branding data is:", window.appBranding);
        const notificationCard = document.createElement('div');
        notificationCard.className = 'notification-card p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
        notificationCard.dataset.id = notification.id;
        notificationCard.dataset.read = notification.read_at ? 'true' : 'false';

        // احصل على بيانات الشركة من المتغير العام
        const branding = window.appBranding || {}; // استخدم كائن فارغ كقيمة افتراضية للأمان
        const logoHtml = branding.logoUrl
            ? `<img src="${branding.logoUrl}" alt="Logo" class="w-8 h-8">`
            : `<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"><i class="fas fa-info-circle text-sm"></i></div>`;

        const titleText = branding.siteName || notification.title;

        notificationCard.innerHTML = `
        <div class="flex items-start space-x-3 rtl:space-x-reverse">
            <div class="flex-shrink-0">
                ${logoHtml}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">
                    ${titleText}
                </p>
                <p class="text-sm text-gray-500">
                    ${notification.message}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    ${this.formatTimestamp(notification.timestamp)}
                </p>
            </div>
        </div>
    `;

        notificationCard.style.animation = 'fadeIn 0.3s ease-out';
        return notificationCard;
    },

    formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.round(diffMs / 60000);
        const diffHours = Math.round(diffMs / 3600000);
        const diffDays = Math.round(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

        return date.toLocaleDateString();
    },
};


// --- منطق التهيئة وجلب البيانات ---
document.addEventListener('DOMContentLoaded', () => {
    if (!window.notificationSystem) return;

    window.notificationSystem.initializeNotifications();

    window.notificationSystem.fetchAndUpdateNotifications = function() {
        if (document.hidden) return;

        fetch('/notifications/latest')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const listElement = this.notificationListElement;
                if (!listElement) return;

                if (this.badgeElement) {
                    const unreadCount = data.unread_count ?? 0;
                    this.badgeElement.textContent = unreadCount;
                    unreadCount > 0 ? this.badgeElement.classList.remove('hidden') : this.badgeElement.classList.add('hidden');
                }

                const existingCardsMap = new Map();
                listElement.querySelectorAll('.notification-card').forEach(card => {
                    if (card && card.dataset.id) existingCardsMap.set(card.dataset.id, card);
                });

                const fragment = document.createDocumentFragment();
                const latestNotificationIds = new Set();

                data.notifications.slice().reverse().forEach(notification => {
                    const notificationId = notification.id.toString();
                    latestNotificationIds.add(notificationId);

                    if (!existingCardsMap.has(notificationId)) {
                        const newElement = this.createNotificationElement({
                            id: notification.id,
                            title: notification.data.title ?? 'New Notification',
                            message: notification.data.message ?? '',
                            timestamp: notification.created_at,
                            read_at: notification.read_at
                        });
                        fragment.appendChild(newElement);
                    } else {
                        const existingCard = existingCardsMap.get(notificationId);
                        existingCard.dataset.read = !!notification.read_at;
                    }
                });

                listElement.prepend(fragment);

                existingCardsMap.forEach((element, id) => {
                    if (!latestNotificationIds.has(id)) element.remove();
                });

                this.updateUIAfterStateLoad();

            }).catch(error => console.error('Error fetching notifications:', error));
    };

    window.notificationSystem.fetchAndUpdateNotifications();
    setInterval(() => window.notificationSystem.fetchAndUpdateNotifications(), 15000);
});


// --- الدوال العالمية ---
window.markAllNotificationsAsRead = function() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(() => {
            document.querySelectorAll('#notification-list .notification-card').forEach(card => {
                card.dataset.read = 'true';
            });
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.classList.add('hidden');
                badge.textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            alert('An error occurred. Please try again.');
        });
};
