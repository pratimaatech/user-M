/**
 * users.js — User list page (index)
 * -----------------------------------
 * Handles:
 *  - AJAX search with debounce
 *  - AJAX pagination
 *  - Per-page selector
 *  - View user modal
 *  - Delete user confirmation
 */

$(function () {
    'use strict';

    // ── State ────────────────────────────────────────────────
    let state = {
        search:      PAGE_DATA.search      || '',
        perPage:     PAGE_DATA.perPage     || 10,
        currentPage: PAGE_DATA.currentPage || 1,
        totalPages:  PAGE_DATA.totalPages  || 1,
        totalUsers:  PAGE_DATA.totalUsers  || 0,
        loading:     false,
    };

    // ── DOM refs ─────────────────────────────────────────────
    const $tableBody   = $('#usersTableBody');
    const $paginNav    = $('#paginationNav');
    const $paginInfo   = $('#paginationInfo');
    const $loading     = $('#tableLoading');
    const $searchInput = $('#searchInput');
    const $perPage     = $('#perPageSelect');

    // ── Search with 350ms debounce ───────────────────────────
    let searchTimer;
    $searchInput.on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            state.search      = $searchInput.val().trim();
            state.currentPage = 1;
            fetchUsers();
        }, 350);
    });

    // ── Per-page change ──────────────────────────────────────
    $perPage.on('change', function () {
        state.perPage     = parseInt($(this).val(), 10);
        state.currentPage = 1;
        fetchUsers();
    });

    // ── Pagination click (delegated) ─────────────────────────
    $paginNav.on('click', '.page-link', function (e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'), 10);
        if (!page || page === state.currentPage) return;
        if (page < 1 || page > state.totalPages) return;
        state.currentPage = page;
        fetchUsers();
    });

    // ── View user modal ──────────────────────────────────────
    const viewModal     = new bootstrap.Modal(document.getElementById('viewUserModal'));
    const $viewBody     = $('#viewUserBody');
    const $modalEditBtn = $('#modalEditLink');

    $tableBody.on('click', '.btn-view', function () {
        const id = $(this).data('id');
        $viewBody.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        viewModal.show();

        $.get('ajax/user_ajax.php', { action: 'get', id: id })
            .done(function (res) {
                if (res.success) {
                    renderViewModal(res.user);
                    $modalEditBtn.attr('href', 'index.php?action=edit&id=' + res.user.id);
                } else {
                    $viewBody.html(`<div class="alert alert-danger">${escHtml(res.message)}</div>`);
                }
            })
            .fail(function () {
                $viewBody.html('<div class="alert alert-danger">Failed to load user data.</div>');
            });
    });

    function renderViewModal(u) {
        const phone = formatPhone(u.phone);
        const created = formatDate(u.created_at);
        const updated = formatDate(u.updated_at);
        const initials = (u.first_name.charAt(0) + u.last_name.charAt(0)).toUpperCase();
            
        // Description List.
        $viewBody.html(`
            <div class="text-center mb-3">
                <div class="user-avatar-sm bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2"
                     style="width:56px;height:56px;font-size:1.2rem">${escHtml(initials)}</div>
                <h5 class="mb-0 fw-bold">${escHtml(u.name_to_use)}</h5>
                <span class="badge bg-secondary">ID #${escHtml(String(u.id))}</span>
            </div>
            <dl class="row g-2 small mb-0">
                <dt class="col-4 text-muted">First Name</dt>
                <dd class="col-8 mb-0 fw-semibold">${escHtml(u.first_name)}</dd>

                <dt class="col-4 text-muted">Last Name</dt>
                <dd class="col-8 mb-0 fw-semibold">${escHtml(u.last_name)}</dd>

                <dt class="col-4 text-muted">Email</dt>
                <dd class="col-8 mb-0">
                    <a href="mailto:${escHtml(u.email)}">${escHtml(u.email)}</a>
                </dd>

                <dt class="col-4 text-muted">Phone</dt>
                <dd class="col-8 mb-0">${escHtml(phone)}</dd>

                <dt class="col-4 text-muted">Created</dt>
                <dd class="col-8 mb-0 text-muted">${escHtml(created)}</dd>

                <dt class="col-4 text-muted">Updated</dt>
                <dd class="col-8 mb-0 text-muted">${escHtml(updated)}</dd>
            </dl>
        `);
    }

    // ── Delete user ──────────────────────────────────────────
    $tableBody.on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title:              `Delete "${name}"?`,
            text:               'This action cannot be undone.',
            icon:               'warning',
            showCancelButton:   true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Yes, delete',
            cancelButtonText:   'Cancel',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.post('ajax/user_ajax.php', {
                action:     'delete',
                id:         id,
                csrf_token: CSRF_TOKEN,
            })
            .done(function (res) {
                if (res.success) {
                    Swal.fire({
                        icon:                'success',
                        title:               'Deleted!',
                        text:                res.message,
                        timer:               1500,
                        showConfirmButton:   false,
                    });

                    // Remove row and refresh if page is now empty
                    const $row = $tableBody.find(`tr[data-id="${id}"]`);
                    $row.fadeOut(300, function () {
                        $row.remove();
                        state.totalUsers--;
                        updateTotalCounter(state.totalUsers);

                        if ($tableBody.find('tr').length === 0) {
                            if (state.currentPage > 1) state.currentPage--;
                            fetchUsers();
                        } else {
                            updatePaginInfo();
                        }
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .fail(function () {
                Swal.fire('Error', 'Server error. Please try again.', 'error');
            });
        });
    });

    // ── Fetch users via AJAX ─────────────────────────────────
    function fetchUsers() {
        if (state.loading) return;
        state.loading = true;
        $loading.removeClass('d-none');

        $.get('ajax/user_ajax.php', {
            action:   'search',
            search:   state.search,
            per_page: state.perPage,
            page:     state.currentPage,
        })
        .done(function (res) {
            if (!res.success) {
                Swal.fire('Error', res.message || 'Could not load users.', 'error');
                return;
            }

            state.totalUsers  = res.total;
            state.totalPages  = res.total_pages;
            state.currentPage = res.current_page;

            renderTableRows(res.users, res.per_page);
            renderPagination();
            updatePaginInfo(res);
            updateTotalCounter(res.total);
        })
        .fail(function () {
            Swal.fire('Error', 'Failed to fetch users. Please try again.', 'error');
        })
        .always(function () {
            state.loading = false;
            $loading.addClass('d-none');
        });
    }

    // ── Render table rows ─────────────────────────────────────
    function renderTableRows(users, perPage) {
        if (!users || users.length === 0) {
            $tableBody.html(`
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        No users found.
                    </td>
                </tr>
            `);
            return;
        }

        const offset = (state.currentPage - 1) * perPage;
        let html = '';

        users.forEach(function (u, i) {
            const initials = (u.first_name.charAt(0) + u.last_name.charAt(0)).toUpperCase();
            const phone    = formatPhone(u.phone);
            const created  = formatDate(u.created_at);

            html += `
            <tr data-id="${escHtml(String(u.id))}">
                <td class="ps-3 text-muted small">${offset + i + 1}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar-sm bg-primary-subtle text-primary rounded-circle">
                            ${escHtml(initials)}
                        </div>
                        <div>
                            <div class="fw-semibold small">${escHtml(u.name_to_use)}</div>
                            <div class="text-muted" style="font-size:.72rem">ID #${escHtml(String(u.id))}</div>
                        </div>
                    </div>
                </td>
                <td class="small">${escHtml(u.email)}</td>
                <td class="small">${escHtml(phone)}</td>
                <td class="small text-muted">${escHtml(created)}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-info btn-view py-0 px-2"
                                data-id="${escHtml(String(u.id))}" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <a href="index.php?action=edit&id=${escHtml(String(u.id))}"
                           class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger btn-delete py-0 px-2"
                                data-id="${escHtml(String(u.id))}"
                                data-name="${escHtml(u.name_to_use)}"
                                title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });

        $tableBody.html(html);
    }

    // ── Render pagination controls ────────────────────────────
    function renderPagination() {
        $paginNav.html(buildPagination(state.currentPage, state.totalPages));
    }

    // ── Update "Showing X–Y of Z records" text ────────────────
    function updatePaginInfo(res) {
        if (!state.totalUsers) {
            $paginInfo.text('No records found');
            return;
        }
        const from = (state.currentPage - 1) * state.perPage + 1;
        const to   = Math.min(state.currentPage * state.perPage, state.totalUsers);
        $paginInfo.text(`Showing ${from}–${to} of ${state.totalUsers.toLocaleString()} records`);
    }

    // ── Update total users dashboard card ─────────────────────
    function updateTotalCounter(total) {
        $('#totalUsersCount').text(total.toLocaleString());
    }
});
