export const isDev = () =>
    document.getElementById('body').getAttribute('data-env') === 'dev' || false
