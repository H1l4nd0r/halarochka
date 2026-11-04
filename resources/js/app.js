import './bootstrap';

import * as bootstrap from 'bootstrap';
// Иконки (Bootstrap Icons): CSS импортируем из JS, чтобы Vite
// корректно собрал @font-face и woff-шрифты в бандл
import 'bootstrap-icons/font/bootstrap-icons.css';

window.bootstrap = bootstrap;

// Тултипы (всплывающие подсказки) для всех элементов с data-bs-toggle="tooltip"
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => new bootstrap.Tooltip(el));
});
