function fromNowShort(date) {
    const duration = moment.duration(moment().diff(date));
    const seconds = duration.asSeconds();
    const minutes = duration.asMinutes();
    const hours = duration.asHours();
    const days = duration.asDays();
    const months = duration.asMonths();
    const years = duration.asYears();
  
    if (seconds < 60) return `${Math.floor(seconds)}s`;
    if (minutes < 60) return `${Math.floor(minutes)}m`;
    if (hours < 24) return `${Math.floor(hours)}h`;
    if (days < 30) return `${Math.floor(days)}d`;
    if (months < 12) return `${Math.floor(months)}mo`;
    return `${Math.floor(years)}y`;
  }

$(document).ready(function() {
    const allDates = $('[data-createdat]');
    for (const dateAt of allDates) {
        const d = dateAt.dataset.createdat;
        dateAt.innerHTML = fromNowShort(moment(d));
    }
});