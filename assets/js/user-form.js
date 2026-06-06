/**
 * user-form.js — Shared form logic for Create and Edit pages
 * ------------------------------------------------------------
 * Handles:
 *  - Real-time display name preview
 *  - Frontend validation
 *  - AJAX form submission (create / update)
 *  - Server-side error display
 *  - Phone number formatting
 */

$(function () {
    'use strict';

    // ── Detect form mode ─────────────────────────────────────
    const $form      = $('#createUserForm, #editUserForm');
    const isEdit     = $form.attr('id') === 'editUserForm';
    const action     = isEdit ? 'update' : 'create';
    const $submitBtn = $('#submitBtn');
    const $spinner   = $('#submitSpinner');
    const $icon      = $('#submitIcon');
    const $alert     = $('#formAlert');

    // ── Live display-name preview ────────────────────────────
    function updateNamePreview() {
        const first = $('#first_name').val().trim();
        const last  = $('#last_name').val().trim();
        const name  = [first, last].filter(Boolean).join(' ');
        $('#namePreview').val(name || '');
    }

    $('#first_name, #last_name').on('input', updateNamePreview);
    updateNamePreview(); // initial render on edit page

    // ── Phone: allow only digits, max 10 ────────────────────
    $('#phone').on('input', function () {
        // Strip non-digits
        let val = $(this).val().replace(/\D/g, '');
        if (val.length > 10) val = val.slice(0, 10);
        $(this).val(val);

        // Live format hint
        if (val.length === 10) {
            const fmt = val.slice(0,3) + '-' + val.slice(3,6) + '-' + val.slice(6);
            $(this).closest('.col-md-6').find('.form-text')
                .html(`<i class="bi bi-check-circle-fill text-success me-1"></i>Will display as <strong>${fmt}</strong>`);
        } else {
            $(this).closest('.col-md-6').find('.form-text')
                .html(`<i class="bi bi-info-circle me-1"></i>Enter exactly 10 digits. Will be formatted as 123-456-7890.`);
        }
    });

    // ── Client-side validation ───────────────────────────────
    function validateForm() {
        let valid = true;

        // Clear previous errors
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        const firstName = $('#first_name').val().trim();
        const lastName  = $('#last_name').val().trim();
        const email     = $('#email').val().trim();
        const phone     = $('#phone').val().replace(/\D/g, '');

        // First name
        if (!firstName) {
            setError('first_name', 'First name is required.');
            valid = false;
        } else if (!/^[A-Za-z\s'\-]+$/.test(firstName)) {
            setError('first_name', 'Only letters, spaces, hyphens, and apostrophes allowed.');
            valid = false;
        }

        // Last name
        if (!lastName) {
            setError('last_name', 'Last name is required.');
            valid = false;
        } else if (!/^[A-Za-z\s'\-]+$/.test(lastName)) {
            setError('last_name', 'Only letters, spaces, hyphens, and apostrophes allowed.');
            valid = false;
        }

        // Email
        if (!email) {
            setError('email', 'Email address is required.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setError('email', 'Please enter a valid email address.');
            valid = false;
        }

        // Phone
        if (!phone) {
            setError('phone', 'Phone number is required.');
            valid = false;
        } else if (phone.length !== 10) {
            setError('phone', 'Phone number must be exactly 10 digits.');
            valid = false;
        }

        return valid;
    }

    function setError(field, message) {
        const $input = $('#' + field);
        $input.addClass('is-invalid');
        $input.siblings('.invalid-feedback').text(message);
    }

    function clearError(field) {
        const $input = $('#' + field);
        $input.removeClass('is-invalid').addClass('is-valid');
        $input.siblings('.invalid-feedback').text('');
    }

    // Mark fields valid on change (after first submit attempt)
    let submitted = false;
    $form.find('input').on('input blur', function () {
        if (!submitted) return;
        const id = $(this).attr('id');
        if (id) $(this).removeClass('is-invalid is-valid');
    });

    // ── Form submit ──────────────────────────────────────────
    $form.on('submit', function (e) {
        e.preventDefault();
        submitted = true;

        if (!validateForm()) {
            $alert.removeClass('d-none alert-success alert-danger')
                  .addClass('alert alert-warning')
                  .html('<i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the highlighted errors before continuing.');
            return;
        }

        $alert.addClass('d-none');
        setLoading(true);

        $.post('ajax/user_ajax.php', $form.serialize())
            .done(function (res) {
                if (res.success) {
                    handleSuccess(res);
                } else if (res.errors) {
                    handleValidationErrors(res.errors);
                } else {
                    showAlert('danger', res.message || 'An error occurred. Please try again.');
                }
            })
            .fail(function () {
                showAlert('danger', 'Server error. Please try again.');
            })
            .always(function () {
                setLoading(false);
            });
    });

    function handleSuccess(res) {
        const verb = isEdit ? 'updated' : 'created';
        Swal.fire({
            icon:              'success',
            title:             'Success!',
            text:              res.message || `User ${verb} successfully.`,
            timer:             2000,
            showConfirmButton: false,
        }).then(function () {
            window.location.href = 'index.php';
        });
    }

    function handleValidationErrors(errors) {
        $.each(errors, function (field, message) {
            setError(field, message);
        });

        showAlert('danger',
            '<i class="bi bi-x-circle-fill me-2"></i>Please correct the errors below and try again.'
        );

        // Scroll to first error
        const $firstError = $form.find('.is-invalid').first();
        if ($firstError.length) {
            $('html, body').animate({
                scrollTop: $firstError.closest('.col-md-6, .col-12').offset().top - 100
            }, 300);
        }
    }

    function showAlert(type, message) {
        $alert.removeClass('d-none alert-success alert-warning alert-danger alert-info')
              .addClass(`alert alert-${type} alert-dismissible`)
              .html(`
                  ${message}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              `);
    }

    function setLoading(loading) {
        if (loading) {
            $submitBtn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $icon.addClass('d-none');
        } else {
            $submitBtn.prop('disabled', false);
            $spinner.addClass('d-none');
            $icon.removeClass('d-none');
        }
    }

    // ── Reset button ─────────────────────────────────────────
    $form.on('reset', function () {
        setTimeout(function () {
            $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $form.find('.invalid-feedback').text('');
            $alert.addClass('d-none');
            updateNamePreview();
            submitted = false;
            // Restore phone hint
            $('#phone').trigger('input');
        }, 10);
    });
});
