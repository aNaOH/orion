/**
 * Formats a date into a short "time ago" string (e.g. 2h, 3d)
 * or a full date if it's older than a week.
 */
function formatOrionDate(date) {
    const now = moment();
    const then = moment(date);
    const diff = now.diff(then); // Positive if in the past
    const duration = moment.duration(Math.abs(diff));

    const seconds = Math.floor(duration.asSeconds());
    const minutes = Math.floor(duration.asMinutes());
    const hours = Math.floor(duration.asHours());
    const days = Math.floor(duration.asDays());

    // If date is in the future (clock skew) or very recent
    if (diff < 0 || seconds < 60) return 'hace un momento';

    // If less than 60 minutes
    if (minutes < 60) return `hace ${minutes}m`;
    // If less than 24 hours
    if (hours < 24) return `hace ${hours}h`;
    // If less than 7 days, show days ago
    if (days < 7) return `hace ${days}d`;

    // If more than 7 days, show the date in a nice format (e.g. 15 may.)
    return `el ${then.format('D MMM')}`;
}

/**
 * Initializes all time-ago elements
 */
function initTimeAgo() {
    if (typeof moment === 'undefined') {
        console.warn('Orion: Moment.js not found yet, retrying...');
        setTimeout(initTimeAgo, 100);
        return;
    }

    const elements = document.querySelectorAll('.time-ago');

    elements.forEach(el => {
        const timestamp = el.getAttribute('data-timestamp');
        if (timestamp) {
            const m = moment.utc(timestamp).local();
            if (m.isValid()) {
                const formatted = formatOrionDate(m);
                el.textContent = formatted;
                el.setAttribute('title', m.format('LLLL'));
                // Prevent PHP fallback from showing if JS fails later
                el.style.visibility = 'visible';
            }
        }
    });
}

// Run on load and also immediately if DOM is already ready
window.addEventListener('load', initTimeAgo);
if (document.readyState === 'complete') {
    initTimeAgo();
}
