/**
 * Live password-rule feedback.
 *
 * Activates on any <input type="password" class="js-password"> that points at
 * a sibling rules list via data-rules="#selector". Toggles "ok" / "miss" classes
 * on each <li data-rule="..."> as the user types. Mirrors Validator::password().
 *
 * Server-side validation remains the source of truth — this script only sharpens
 * the feedback loop in the form.
 */
(function () {
    'use strict';

    const RULES = {
        length:  (v) => v.length >= 8 && v.length <= 72,
        lower:   (v) => /[a-z]/.test(v),
        upper:   (v) => /[A-Z]/.test(v),
        digit:   (v) => /\d/.test(v),
        special: (v) => /[^A-Za-z0-9]/.test(v),
        nospace: (v) => v.length > 0 && !/\s/.test(v),
    };

    function evaluate(value, listEl) {
        listEl.querySelectorAll('li[data-rule]').forEach((li) => {
            const rule = li.dataset.rule;
            const fn = RULES[rule];
            if (!fn) return;
            const ok = fn(value);
            li.classList.toggle('ok', ok);
            li.classList.toggle('miss', !ok && value.length > 0);
        });
    }

    function bind(input) {
        const selector = input.dataset.rules;
        const list = selector ? document.querySelector(selector) : null;
        if (!list) return;
        input.addEventListener('input', () => evaluate(input.value, list));
        // initial pass — accounts for autofilled values
        evaluate(input.value, list);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input.js-password').forEach(bind);
    });
})();
