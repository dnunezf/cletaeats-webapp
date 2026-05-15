<?php
/**
 * Inline help block listing the strong-password rules.
 *
 * Drop this in below a password input that opts in to live validation:
 *     <input type="password" name="password" class="form-input js-password" data-rules="#pwRules">
 *     <div id="pwRules"><?php require BASE_PATH . '/views/partials/password-rules.php'; ?></div>
 *
 * Server-side validation in Validator::password() is the source of truth.
 */
?>
<ul class="password-rules" aria-live="polite">
    <li data-rule="length">At least 8 characters (max 72)</li>
    <li data-rule="lower">One lowercase letter (a–z)</li>
    <li data-rule="upper">One uppercase letter (A–Z)</li>
    <li data-rule="digit">One digit (0–9)</li>
    <li data-rule="special">One special character (e.g. !@#$%^&amp;*)</li>
    <li data-rule="nospace">No spaces</li>
</ul>
