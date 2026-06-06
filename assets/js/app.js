/**
 * app.js — Global JavaScript for User Management System
 * -------------------------------------------------------
 * Handles:
 *  - Sidebar toggle (mobile)
 *  - Global AJAX error handling
 *  - Shared utility functions
 */

$(function () {
    'use strict';

    // ── Sidebar toggle (mobile) ──────────────────────────────
    const $sidebar  = $('#sidebar');
    const $overlay  = $('#sidebarOverlay');
    const $toggle   = $('#sidebarToggle');

    $toggle.on('click', function () {
        $sidebar.toggleClass('show');
        $overlay.toggleClass('show');
    });

    $overlay.on('click', function () {
        $sidebar.removeClass('show');
        $overlay.removeClass('show');
    });

    // Close sidebar on ESC
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $sidebar.removeClass('show');
            $overlay.removeClass('show');
        }
    });

    // ── Active nav link highlight ────────────────────────────
    const currentHref = window.location.href;
    $('#sidebar .nav-link').each(function () {
        if (currentHref === $(this).prop('href')) {
            $(this).addClass('active bg-primary');
        }
    });

    // ── Global AJAX setup ────────────────────────────────────
    $.ajaxSetup({
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    // Show SweetAlert on global AJAX failures (network errors etc.)
    $(document).ajaxError(function (_event, jqXHR) {
        if (jqXHR.status === 0) return; // Aborted request (page navigation)
        if (jqXHR.status === 403) {
            Swal.fire({
                icon:  'error',
                title: 'Access Denied',
                text:  'Your session may have expired. Please refresh the page.',
            });
        }
    });
});

/* ── Utility Functions (global) ──────────────────────────────── */


function formatPhone(phone) {
    const digits = phone.replace(/\D/g, '');
    if (digits.length !== 10) return phone;
    return digits.slice(0, 3) + '-' + digits.slice(3, 6) + '-' + digits.slice(6);
}


function formatDate(datetime) {
    if (!datetime) return '—';
    const d = new Date(datetime.replace(' ', 'T'));
    return d.toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
}


function buildPagination(currentPage, totalPages) {
    if (totalPages <= 1) return '';

    let html = '<ul class="pagination pagination-sm mb-0">';

    // Prev
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage - 1}">
            <i class="bi bi-chevron-left"></i>
        </a>
    </li>`;

    const start = Math.max(1, currentPage - 2);
    const end   = Math.min(totalPages, currentPage + 2);

    if (start > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
        if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }

    for (let p = start; p <= end; p++) {
        html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${p}">${p}</a>
        </li>`;
    }

    if (end < totalPages) {
        if (end < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
    }

    // Next
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage + 1}">
            <i class="bi bi-chevron-right"></i>
        </a>
    </li>`;

    html += '</ul>';
    return html;
}

    
function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}
